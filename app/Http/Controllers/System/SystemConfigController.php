<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemConfigController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('system.view'), 403);
        $configs = SystemConfig::orderBy('group')->orderBy('key')->paginate(20);

        return view('system.configs.index', compact('configs'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('system.create'), 403);

        return view('system.configs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('system.create'), 403);
        $data = $request->validate(['key' => 'required|unique:system_configs,key', 'value' => 'nullable', 'group' => 'nullable']);
        SystemConfig::create($data);

        return redirect()->route('system.configs.index')->with('success', 'Đã thêm cấu hình');
    }

    public function edit(SystemConfig $config): View
    {
        abort_unless(auth()->user()->can('system.update'), 403);

        return view('system.configs.edit', compact('config'));
    }

    public function update(Request $request, SystemConfig $config): RedirectResponse
    {
        abort_unless(auth()->user()->can('system.update'), 403);
        $config->update($request->validate(['value' => 'nullable', 'group' => 'nullable']));

        return redirect()->route('system.configs.index')->with('success', 'Cập nhật cấu hình');
    }
}
