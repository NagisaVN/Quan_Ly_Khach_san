<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $result = $this->authService->authenticate(
            $request->validated('email'),
            $request->validated('password'),
            $request,
        );

        if ($result['status'] === 'requires_2fa') {
            $request->session()->put('two_factor_user_id', $result['user']->id);
            $request->session()->put('login.remember', $request->boolean('remember'));

            return redirect()->route('two-factor.show');
        }

        if ($result['status'] === 'success') {
            Auth::login($result['user'], $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => $result['message']]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout($request->user(), $request);

        return redirect()->route('login');
    }
}
