<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BackupController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('system.view'), 403);
        $backups = Backup::orderByDesc('id')->paginate(15);

        return view('system.backups.index', compact('backups'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('system.create'), 403);
        Backup::create([
            'filename' => 'backup-'.now()->format('Ymd-His').'.sql',
            'path' => 'backups/backup-'.Str::random(8).'.sql',
            'size' => 0,
            'status' => 'completed',
            'notes' => 'Mock backup generated',
        ]);

        return back()->with('success', 'Đã tạo backup (mock)');
    }
}
