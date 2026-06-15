<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginLogController extends Controller
{
    public function index(Request $request): View
    {
        $loginLogs = LoginLog::query()
            ->with('user')
            ->when($request->filled('email'), fn ($query) => $query->where('email', 'like', '%'.$request->string('email').'%'))
            ->when($request->filled('success') && $request->input('success') !== '', function ($query) use ($request) {
                $query->where('success', $request->boolean('success'));
            })
            ->when($request->filled('date'), fn ($query) => $query->whereDate('created_at', $request->date('date')))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('security.login-logs.index', compact('loginLogs'));
    }
}
