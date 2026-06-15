<?php

namespace Tests;

use App\Enums\RoomStatus;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

trait CreatesHotelTestData
{
    use RefreshDatabase;

    protected Company $company;

    protected Branch $branch;

    protected RoomType $roomType;

    protected Room $room;

    protected Customer $customer;

    protected User $receptionist;

    protected function setUpHotelTestData(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->company = Company::create([
            'name' => 'Test Hotel Co',
            'code' => 'TST',
            'is_active' => true,
        ]);

        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'name' => 'Test Branch',
            'code' => 'TB',
            'is_active' => true,
        ]);

        $this->roomType = RoomType::create([
            'company_id' => $this->company->id,
            'name' => 'Standard',
            'code' => 'STD',
            'base_price' => 1000000,
            'max_occupancy' => 2,
            'is_active' => true,
        ]);

        $floor = Floor::create([
            'branch_id' => $this->branch->id,
            'name' => 'Floor 1',
            'floor_number' => 1,
            'is_active' => true,
        ]);

        $this->room = Room::create([
            'branch_id' => $this->branch->id,
            'floor_id' => $floor->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '101',
            'status' => RoomStatus::Available,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'CUS-TEST-001',
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'phone' => '0900000001',
            'is_active' => true,
        ]);

        $this->receptionist = User::factory()->create([
            'email' => 'receptionist@test.vn',
            'password' => Hash::make('password'),
            'company_id' => $this->company->id,
            'current_branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        $this->receptionist->assignRole('receptionist');
        $this->receptionist->branches()->attach($this->branch->id, ['is_default' => true]);
    }
}
