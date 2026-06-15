<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $canViewAll = $user->hasRole('super_admin') && $user->can('security.view');

        $query = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->select(
                'sessions.id',
                'sessions.user_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.name as user_name',
                'users.email as user_email',
            )
            ->orderByDesc('sessions.last_activity');

        if (! $canViewAll) {
            $query->where('sessions.user_id', $user->id);
        }

        $sessions = $query->get()->map(function ($session) use ($request) {
            $session->is_current = $session->id === $request->session()->getId();
            $session->last_activity_at = \Carbon\Carbon::createFromTimestamp($session->last_activity);

            return $session;
        });

        return view('security.sessions.index', [
            'sessions' => $sessions,
            'canViewAll' => $canViewAll,
        ]);
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        $session = DB::table('sessions')->where('id', $id)->first();

        if ($session === null) {
            abort(404);
        }

        $user = $request->user();
        $canViewAll = $user->hasRole('super_admin') && $user->can('security.view');

        if (! $canViewAll && (int) $session->user_id !== $user->id) {
            abort(403);
        }

        if ($session->id === $request->session()->getId()) {
            return redirect()
                ->route('security.sessions.index')
                ->with('error', 'Không thể đăng xuất phiên hiện tại.');
        }

        DB::table('sessions')->where('id', $id)->delete();

        return redirect()
            ->route('security.sessions.index')
            ->with('success', 'Đã đăng xuất phiên thiết bị thành công.');
    }
}
