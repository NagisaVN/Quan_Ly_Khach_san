<?php

namespace Database\Seeders;

use App\Enums\RoomStatus;
use App\Models\Amenity;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\LoyaltyTier;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::create([
            'name' => 'Khách sạn Demo Grand',
            'code' => 'DEMO',
            'tax_code' => '0123456789',
            'email' => 'info@demogrand.vn',
            'phone' => '0281234567',
            'address' => '123 Nguyễn Huệ, Quận 1, TP.HCM',
            'is_active' => true,
        ]);

        $branchHcm = Branch::create([
            'company_id' => $company->id,
            'name' => 'Chi nhánh TP.HCM',
            'code' => 'HCM',
            'address' => '123 Nguyễn Huệ, Quận 1, TP.HCM',
            'phone' => '0281234567',
            'email' => 'hcm@demogrand.vn',
            'is_active' => true,
        ]);

        $branchDn = Branch::create([
            'company_id' => $company->id,
            'name' => 'Chi nhánh Đà Nẵng',
            'code' => 'DN',
            'address' => '456 Bạch Đằng, Hải Châu, Đà Nẵng',
            'phone' => '0236123456',
            'email' => 'dn@demogrand.vn',
            'is_active' => true,
        ]);

        $standardType = RoomType::create([
            'company_id' => $company->id,
            'name' => 'Standard',
            'code' => 'STD',
            'description' => 'Phòng tiêu chuẩn 2 giường đơn',
            'max_occupancy' => 2,
            'max_adults' => 2,
            'max_children' => 1,
            'base_price' => 800000,
            'area_sqm' => 25,
            'is_active' => true,
        ]);

        $deluxeType = RoomType::create([
            'company_id' => $company->id,
            'name' => 'Deluxe',
            'code' => 'DLX',
            'description' => 'Phòng deluxe view thành phố',
            'max_occupancy' => 3,
            'max_adults' => 2,
            'max_children' => 2,
            'base_price' => 1200000,
            'area_sqm' => 35,
            'is_active' => true,
        ]);

        $wifi = Amenity::create([
            'company_id' => $company->id,
            'name' => 'WiFi',
            'icon' => 'wifi',
            'is_active' => true,
        ]);

        $tv = Amenity::create([
            'company_id' => $company->id,
            'name' => 'Smart TV',
            'icon' => 'tv',
            'is_active' => true,
        ]);

        LoyaltyTier::create([
            'company_id' => $company->id,
            'name' => 'Silver',
            'code' => 'SILVER',
            'min_points' => 0,
            'discount_percent' => 0,
            'is_active' => true,
        ]);

        $goldTier = LoyaltyTier::create([
            'company_id' => $company->id,
            'name' => 'Gold',
            'code' => 'GOLD',
            'min_points' => 1000,
            'discount_percent' => 5,
            'is_active' => true,
        ]);

        $this->seedBranchRooms($branchHcm, $standardType, $deluxeType, [$wifi, $tv]);
        $this->seedBranchRooms($branchDn, $standardType, $deluxeType, [$wifi, $tv]);

        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@demo.vn', 'role' => 'super_admin', 'branch' => $branchHcm],
            ['name' => 'Admin Công ty', 'email' => 'admin@demo.vn', 'role' => 'company_admin', 'branch' => $branchHcm],
            ['name' => 'Quản lý KS HCM', 'email' => 'manager.hcm@demo.vn', 'role' => 'hotel_manager', 'branch' => $branchHcm],
            ['name' => 'Quản lý KS ĐN', 'email' => 'manager.dn@demo.vn', 'role' => 'hotel_manager', 'branch' => $branchDn],
            ['name' => 'Lễ tân HCM', 'email' => 'letan.hcm@demo.vn', 'role' => 'receptionist', 'branch' => $branchHcm],
            ['name' => 'Lễ tân ĐN', 'email' => 'letan.dn@demo.vn', 'role' => 'receptionist', 'branch' => $branchDn],
            ['name' => 'Nhân viên Buồng phòng', 'email' => 'staff@demo.vn', 'role' => 'staff', 'branch' => $branchHcm],
            ['name' => 'Khách hàng Demo', 'email' => 'customer@demo.vn', 'role' => 'customer', 'branch' => $branchHcm],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'phone' => '0900000000',
                'company_id' => $company->id,
                'current_branch_id' => $userData['branch']->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $user->assignRole($userData['role']);
            $user->branches()->attach($userData['branch']->id, ['is_default' => true]);

            if ($userData['role'] === 'company_admin') {
                $user->branches()->attach($branchDn->id, ['is_default' => false]);
            }
        }

        Customer::create([
            'company_id' => $company->id,
            'branch_id' => $branchHcm->id,
            'user_id' => User::where('email', 'customer@demo.vn')->first()->id,
            'loyalty_tier_id' => $goldTier->id,
            'code' => 'CUS001',
            'first_name' => 'Nguyễn',
            'last_name' => 'Văn A',
            'email' => 'customer@demo.vn',
            'phone' => '0912345678',
            'loyalty_points' => 1500,
            'is_active' => true,
        ]);
    }

    private function seedBranchRooms(
        Branch $branch,
        RoomType $standardType,
        RoomType $deluxeType,
        array $amenities
    ): void {
        for ($floorNum = 1; $floorNum <= 2; $floorNum++) {
            $floor = Floor::create([
                'branch_id' => $branch->id,
                'name' => "Tầng {$floorNum}",
                'floor_number' => $floorNum,
                'is_active' => true,
            ]);

            for ($roomNum = 1; $roomNum <= 4; $roomNum++) {
                $roomNumber = sprintf('%d%02d', $floorNum, $roomNum);
                $type = $roomNum <= 2 ? $standardType : $deluxeType;

                $room = Room::create([
                    'branch_id' => $branch->id,
                    'floor_id' => $floor->id,
                    'room_type_id' => $type->id,
                    'room_number' => $roomNumber,
                    'status' => RoomStatus::Available,
                    'is_active' => true,
                ]);

                $room->amenities()->sync(collect($amenities)->pluck('id'));
            }
        }
    }
}
