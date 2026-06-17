<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaintenanceRequestRequest;
use App\Http\Requests\UpdateMaintenanceRequestRequest;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Services\MaintenanceRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRequestController extends Controller
{
    public function __construct(private MaintenanceRequestService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', MaintenanceRequest::class);
        $requests = $this->service->paginate($request->only('search', 'status', 'priority'));

        return view('maintenance.requests.index', compact('requests'));
    }

    public function create(): View
    {
        $this->authorize('create', MaintenanceRequest::class);
        $rooms = Room::where('branch_id', session('current_branch_id'))
            ->where('is_active', true)
            ->orderBy('room_number')
            ->get();

        return view('maintenance.requests.create', compact('rooms'));
    }

    public function store(StoreMaintenanceRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['branch_id'] = session('current_branch_id');
        $item = $this->service->create($data);

        return redirect()->route('maintenance.requests.show', $item)
            ->with('success', 'Tạo yêu cầu bảo trì thành công.');
    }

    public function show(MaintenanceRequest $maintenanceRequest): View
    {
        $this->authorize('view', $maintenanceRequest);
        $maintenanceRequest->load('room');

        return view('maintenance.requests.show', compact('maintenanceRequest'));
    }

    public function edit(MaintenanceRequest $maintenanceRequest): View
    {
        $this->authorize('update', $maintenanceRequest);

        return view('maintenance.requests.edit', compact('maintenanceRequest'));
    }

    public function update(UpdateMaintenanceRequestRequest $request, MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        $this->authorize('update', $maintenanceRequest);
        $this->service->update($maintenanceRequest, $request->validated());

        return redirect()->route('maintenance.requests.show', $maintenanceRequest)
            ->with('success', 'Cập nhật yêu cầu thành công.');
    }

    public function destroy(MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        $this->authorize('delete', $maintenanceRequest);
        $this->service->delete($maintenanceRequest);

        return redirect()->route('maintenance.requests.index')
            ->with('success', 'Xóa yêu cầu thành công.');
    }
}
