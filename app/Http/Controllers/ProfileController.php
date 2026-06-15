<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function show()
    {
        $user = Auth::user();
        
        return view('profile.show', [
            'user' => $user,
            'loginHistory' => $user->loginLogs()
                ->latest()
                ->limit(10)
                ->get(),
            'activeSessions' => session('_previous.url') ? 1 : 0,
        ]);
    }

    /**
     * Show edit profile page
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Cập nhật hồ sơ thành công');
    }

    /**
     * Show security settings page
     */
    public function security()
    {
        $user = Auth::user();
        
        // Get active sessions from activity_logs (where user is logged in - most recent logins)
        $loginSessions = ActivityLog::where('user_id', $user->id)
            ->whereIn('action', ['login', 'logout'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('profile.security', [
            'user' => $user,
            'twoFactorEnabled' => $user->two_factor_enabled,
            'loginSessions' => $loginSessions,
            'failedLogins' => $user->failed_login_attempts ?? 0,
            'isLocked' => $user->is_account_locked,
        ]);
    }

    /**
     * Change password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.security')
            ->with('success', 'Mật khẩu được cập nhật thành công');
    }

    /**
     * Logout from other sessions
     */
    public function logoutOtherSessions(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        
        // Logout from other sessions
        \DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->delete();

        return redirect()->back()
            ->with('success', 'Tất cả các phiên đăng nhập khác đã được thoát');
    }

    /**
     * Show notification preferences
     */
    public function notifications()
    {
        return view('profile.notifications', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        $preferences = [
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'booking_notifications' => $request->boolean('booking_notifications'),
            'payment_notifications' => $request->boolean('payment_notifications'),
            'system_notifications' => $request->boolean('system_notifications'),
        ];

        $user->update([
            'notification_preferences' => json_encode($preferences),
        ]);

        return redirect()->back()
            ->with('success', 'Cập nhật tùy chọn thông báo thành công');
    }

    /**
     * Show login history
     */
    public function loginHistory()
    {
        $user = Auth::user();
        
        return view('profile.login-history', [
            'sessions' => $user->loginLogs()
                ->orderBy('login_at', 'desc')
                ->paginate(20),
        ]);
    }

    /**
     * Logout from specific session
     */
    public function logoutSession(Request $request, $sessionId)
    {
        $this->authorize('logout-session', Auth::user());
        
        \DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->back()
            ->with('success', 'Phiên đăng nhập đã bị thoát');
    }
}
