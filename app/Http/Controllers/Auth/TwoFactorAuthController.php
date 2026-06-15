<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    /**
     * Show 2FA setup page
     */
    public function setup()
    {
        $user = Auth::user();
        
        if ($user->two_factor_enabled) {
            return redirect()->route('profile.security')
                ->with('info', 'Bạn đã kích hoạt xác thực 2 yếu tố');
        }

        // Generate secret key
        $secret = $this->google2fa->generateSecretKey();
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.two-factor.setup', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Enable 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        
        // Verify code
        $verify = $this->google2fa->verifyKey($request->secret, $request->code);
        
        if (!$verify) {
            return redirect()->back()
                ->withErrors(['code' => 'Mã xác thực không chính xác']);
        }

        // Save secret and enable 2FA
        $user->update([
            'two_factor_secret' => encrypt($request->secret),
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        return redirect()->route('profile.security')
            ->with('success', 'Bạn đã kích hoạt xác thực 2 yếu tố thành công');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
        ]);

        return redirect()->route('profile.security')
            ->with('success', 'Bạn đã vô hiệu hóa xác thực 2 yếu tố');
    }

    /**
     * Show 2FA verification page during login
     */
    public function showVerify(Request $request)
    {
        if (!$request->session()->has('auth.2fa.pending')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor.verify');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        if (!$request->session()->has('auth.2fa.pending')) {
            return redirect()->route('login');
        }

        $user = User::find($request->session()->get('auth.2fa.user_id'));
        
        if (!$user || !$user->two_factor_secret) {
            return redirect()->route('login');
        }

        // Decrypt the secret
        $secret = decrypt($user->two_factor_secret);
        
        // Verify code
        $verify = $this->google2fa->verifyKey($secret, $request->code, window: 1);
        
        if (!$verify) {
            return redirect()->back()
                ->withErrors(['code' => 'Mã xác thực không chính xác']);
        }

        // Complete login
        Auth::login($user, $request->boolean('remember'));
        $request->session()->forget(['auth.2fa.pending', 'auth.2fa.user_id']);
        $request->session()->put('2fa_verified', true);
        
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Backup codes - generate and show
     */
    public function backupCodes()
    {
        $user = Auth::user();
        
        if (!$user->two_factor_enabled) {
            return redirect()->route('profile.security');
        }

        // Generate 10 backup codes (5 digits each, hyphens between groups)
        $codes = array_map(function () {
            return substr(md5(random_bytes(16)), 0, 8);
        }, range(1, 10));

        session(['backup_codes' => $codes]);

        return view('auth.two-factor.backup-codes', [
            'codes' => $codes,
        ]);
    }
}
