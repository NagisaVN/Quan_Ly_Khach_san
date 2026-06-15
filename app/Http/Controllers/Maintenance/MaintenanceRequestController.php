<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRequestController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('maintenance.view'), 403);
        $requests = MaintenanceRequest::query()->with('room')
            ->where('branch_id', session('current_branch_id'))
            ->orderByDesc('id')->paginate(15);

        return view('maintenance.requests.index', compact('requests'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('maintenance.create'), 403);
        $rooms = Room::where('branch_id', session('current_branch_id'))->where('is_active', true)->orderBy('room_number')->get();

        return view('maintenance.requests.create', compact('rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('maintenance.create'), 403);
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);
        $data['branch_id'] = session('current_branch_id');
        $data['status'] = 'pending';
        $data['reported_at'] = now();
        $item = MaintenanceRequest::create($data);

        return redirect()->route('maintenance.requests.show', $item)->with('success', 'Tạo yêu cầu bảo trì');
    }

    public function show(MaintenanceRequest $maintenanceRequest): View
    {
        abort_unless(auth()->user()->can('maintenance.view'), 403);
        $maintenanceRequest->load('room');

        return view('maintenance.requests.show', compact('maintenanceRequest'));
    }

    public function edit(MaintenanceRequest $maintenanceRequest): View
    {
        abort_unless(auth()->user()->can('maintenance.update'), 403);

        return view('maintenance.requests.edit', compact('maintenanceRequest'));
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        abort_unless(auth()->user()->can('maintenance.update'), 403);
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'resolution_notes' => 'nullable|string',
        ]);
        if ($data['status'] === 'in_progress' && ! $maintenanceRequest->started_at) {
            $data['started_at'] = now();
        }
        if ($data['status'] === 'completed') {
            $data['completed_at'] = now();
        }
        $maintenanceRequest->update($data);

        return redirect()->route('maintenance.requests.show', $maintenanceRequest)->with('success', 'Cập nhật bảo trì');
    }

    public function destroy(MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        abort_unless(auth()->user()->can('maintenance.delete'), 403);
        $maintenanceRequest->delete();

        return redirect()->route('maintenance.requests.index')->with('success', 'Đã xóa');
    }
}
