<?php

namespace Tests\Feature\Auth;

use Tests\CreatesHotelTestData;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use CreatesHotelTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpHotelTestData();
    }

    public function test_receptionist_can_access_bookings_index(): void
    {
        $this->actingAs($this->receptionist)
            ->withSession(['current_branch_id' => $this->branch->id])
            ->get(route('bookings.index'))
            ->assertOk();
    }

    public function test_receptionist_cannot_access_system_users_without_permission(): void
    {
        $guest = \App\Models\User::factory()->create([
            'company_id' => $this->company->id,
            'current_branch_id' => $this->branch->id,
            'is_active' => true,
        ]);
        $guest->assignRole('customer');

        $this->actingAs($guest)
            ->withSession(['current_branch_id' => $this->branch->id])
            ->get(route('system.users.index'))
            ->assertForbidden();
    }
}
