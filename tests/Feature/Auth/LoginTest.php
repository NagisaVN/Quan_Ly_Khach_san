<?php

namespace Tests\Feature\Auth;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('login_logs', [
            'user_id' => $user->id,
            'email' => 'test@example.com',
            'success' => true,
        ]);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $user->refresh();
        $this->assertEquals(1, $user->failed_login_attempts);

        $this->assertDatabaseHas('login_logs', [
            'user_id' => $user->id,
            'email' => 'test@example.com',
            'success' => false,
            'failure_reason' => 'invalid_credentials',
        ]);
    }

    public function test_locked_account_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'locked@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'failed_login_attempts' => 5,
            'locked_until' => now()->addMinutes(30),
        ]);

        $response = $this->post('/login', [
            'email' => 'locked@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->assertDatabaseHas('login_logs', [
            'user_id' => $user->id,
            'email' => 'locked@example.com',
            'success' => false,
            'failure_reason' => 'account_locked',
        ]);
    }
}
