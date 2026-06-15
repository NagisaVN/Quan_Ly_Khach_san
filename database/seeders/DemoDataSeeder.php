<?php

namespace Database\Seeders;

use App\Enums\RoomStatus;
use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Models\Amenity;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LoyaltyTier;
use App\Models\Payment;
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

        // Create sample bookings and payments
        $this->createSampleBookings($company, $branchHcm, $standardType, $deluxeType);
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

    private function createSampleBookings(
        Company $company,
        Branch $branch,
        RoomType $standardType,
        RoomType $deluxeType
    ): void {
        $customer = Customer::where('email', 'customer@demo.vn')->first();
        $receiptionist = User::where('email', 'letan.hcm@demo.vn')->first();
        
        // Booking 1: Today check-in (status: confirmed)
        $booking1 = Booking::create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'booking_code' => 'BK001',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'status' => BookingStatus::Confirmed->value,
            'adults' => 2,
            'children' => 0,
            'total_amount' => 1600000,
            'special_requests' => 'Khách đặt sớm',
            'created_by' => $receiptionist->id,
        ]);

        // Assign room to booking
        $room = Room::where('branch_id', $branch->id)->first();
        BookingRoom::create([
            'booking_id' => $booking1->id,
            'room_id' => $room->id,
            'room_type_id' => $standardType->id,
            'check_in_date' => $booking1->check_in_date,
            'check_out_date' => $booking1->check_out_date,
            'rate_snapshot' => 800000,
            'total_amount' => 1600000,
            'nights' => 2,
        ]);

        // Create invoice for booking 1
        $invoice1 = Invoice::create([
            'branch_id' => $branch->id,
            'booking_id' => $booking1->id,
            'customer_id' => $customer->id,
            'invoice_number' => 'INV001',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(1)->toDateString(),
            'subtotal' => 1600000,
            'tax_amount' => 160000,
            'total_amount' => 1760000,
            'status' => 'draft',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'item_type' => 'room',
            'reference_id' => $room->id,
            'description' => 'Standard Room - 2 nights',
            'quantity' => 2,
            'unit_price' => 800000,
            'total_amount' => 1600000,
        ]);

        // Create payment for invoice 1 (today)
        Payment::create([
            'invoice_id' => $invoice1->id,
            'branch_id' => $branch->id,
            'amount' => 1760000,
            'payment_method' => PaymentMethod::Bank->value,
            'paid_at' => now(),
            'status' => 'completed',
            'reference' => 'PAY001',
            'payment_number' => 'PAY001',
        ]);

        // Booking 2: Tomorrow check-in (status: pending)
        $booking2 = Booking::create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'booking_code' => 'BK002',
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(4)->toDateString(),
            'status' => BookingStatus::Pending->value,
            'adults' => 2,
            'children' => 1,
            'total_amount' => 3600000,
            'special_requests' => 'Đặt qua điện thoại',
            'created_by' => $receiptionist->id,
        ]);

        // Assign room to booking 2
        $room2 = Room::where('branch_id', $branch->id)->skip(1)->first();
        BookingRoom::create([
            'booking_id' => $booking2->id,
            'room_id' => $room2->id,
            'room_type_id' => $deluxeType->id,
            'check_in_date' => $booking2->check_in_date,
            'check_out_date' => $booking2->check_out_date,
            'rate_snapshot' => 1200000,
            'total_amount' => 3600000,
            'nights' => 3,
        ]);

        // Booking 3: Past booking checked out (status: checked-out)
        $booking3 = Booking::create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'booking_code' => 'BK003',
            'check_in_date' => now()->subDays(3)->toDateString(),
            'check_out_date' => now()->subDay()->toDateString(),
            'status' => BookingStatus::CheckedOut->value,
            'adults' => 2,
            'children' => 0,
            'total_amount' => 2400000,
            'special_requests' => 'Khách cũ',
            'created_by' => $receiptionist->id,
        ]);

        // Create invoice and payment for booking 3
        $invoice3 = Invoice::create([
            'branch_id' => $branch->id,
            'booking_id' => $booking3->id,
            'customer_id' => $customer->id,
            'invoice_number' => 'INV003',
            'issue_date' => now()->subDays(3)->toDateString(),
            'due_date' => now()->subDays(2)->toDateString(),
            'subtotal' => 2400000,
            'tax_amount' => 240000,
            'total_amount' => 2640000,
            'status' => 'paid',
        ]);

        Payment::create([
            'invoice_id' => $invoice3->id,
            'branch_id' => $branch->id,
            'amount' => 2640000,
            'payment_method' => PaymentMethod::Bank->value,
            'paid_at' => now()->subDay(),
            'status' => 'completed',
            'reference' => 'PAY003',
            'payment_number' => 'PAY003',
        ]);

        // Create additional payments for the week (revenue trend data)
        for ($i = 1; $i <= 5; $i++) {
            $paymentDate = now()->subDays($i);
            // Skip weekends for more realistic data
            if ($paymentDate->dayOfWeek !== 0 && $paymentDate->dayOfWeek !== 6) {
                // Create a simple invoice for standalone payments
                $standaloneInvoice = Invoice::create([
                    'branch_id' => $branch->id,
                    'customer_id' => $customer->id,
                    'invoice_number' => 'INV' . str_pad(100 + $i, 3, '0', STR_PAD_LEFT),
                    'issue_date' => $paymentDate->toDateString(),
                    'due_date' => $paymentDate->toDateString(),
                    'total_amount' => rand(800000, 2000000),
                    'subtotal' => rand(800000, 2000000),
                    'status' => 'paid',
                ]);

                Payment::create([
                    'invoice_id' => $standaloneInvoice->id,
                    'branch_id' => $branch->id,
                    'amount' => $standaloneInvoice->total_amount,
                    'payment_method' => collect(PaymentMethod::cases())->random()->value,
                    'paid_at' => $paymentDate,
                    'status' => 'completed',
                    'payment_number' => 'PAY' . str_pad(100 + $i, 3, '0', STR_PAD_LEFT),
                    'reference' => 'PAY' . str_pad(100 + $i, 3, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }
}
