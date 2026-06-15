<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('system.view'), 403);
        $logs = ActivityLog::query()->orderByDesc('id')->paginate(30);

        return view('system.activity-logs.index', compact('logs'));
    }
}
