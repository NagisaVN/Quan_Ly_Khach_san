<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\LoginLogRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private const MAX_FAILED_ATTEMPTS = 5;

    private const LOCK_MINUTES = 30;

    public function __construct(
        private readonly LoginLogRepositoryInterface $loginLogRepository,
    ) {}

    /**
     * @return array{status: string, user?: User, message?: string}
     */
    public function authenticate(string $email, string $password, Request $request): array
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->logAttempt(null, $email, $request, false, 'invalid_credentials');

            return [
                'status' => 'invalid_credentials',
                'message' => 'Email hoặc mật khẩu không đúng.',
            ];
        }

        if ($user->isLocked()) {
            $this->logAttempt($user, $email, $request, false, 'account_locked');

            return [
                'status' => 'account_locked',
                'message' => 'Tài khoản đã bị khóa do đăng nhập sai quá nhiều lần. Vui lòng thử lại sau 30 phút.',
            ];
        }

        if (! $user->is_active) {
            $this->logAttempt($user, $email, $request, false, 'account_inactive');

            return [
                'status' => 'account_inactive',
                'message' => 'Tài khoản đã bị vô hiệu hóa.',
            ];
        }

        if (! Hash::check($password, $user->password)) {
            DB::transaction(function () use ($user) {
                $user->incrementFailedAttempts();
            });

            $this->logAttempt($user, $email, $request, false, 'invalid_credentials');

            return [
                'status' => 'invalid_credentials',
                'message' => 'Email hoặc mật khẩu không đúng.',
            ];
        }

        DB::transaction(function () use ($user) {
            $user->resetFailedAttempts();
        });

        $this->logAttempt($user, $email, $request, true);

        if ($this->requiresTwoFactor($user)) {
            return [
                'status' => 'requires_2fa',
                'user' => $user,
            ];
        }

        return [
            'status' => 'success',
            'user' => $user,
        ];
    }

    public function requiresTwoFactor(User $user): bool
    {
        return $user->two_factor_enabled && ! empty($user->two_factor_secret);
    }

    public function completeTwoFactor(User $user, $session): void
    {
        $remember = (bool) $session->get('login.remember', false);

        Auth::login($user, $remember);

        $session->forget(['two_factor_user_id', 'login.remember']);
        $session->regenerate();
    }

    public function logout(?User $user, Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    private function logAttempt(
        ?User $user,
        string $email,
        Request $request,
        bool $success,
        ?string $failureReason = null,
    ): void {
        $this->loginLogRepository->create([
            'user_id' => $user?->id,
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'success' => $success,
            'failure_reason' => $failureReason,
        ]);
    }
}
