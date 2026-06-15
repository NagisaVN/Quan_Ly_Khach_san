<?php

namespace App\Http\Controllers\Rooms;

use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rooms\StoreFloorRequest;
use App\Http\Requests\Rooms\UpdateFloorRequest;
use App\Models\Floor;
use App\Services\FloorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FloorController extends Controller
{
    public function __construct(private FloorService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Floor::class);

        $floors = $this->service->paginate($request->only('search'));

        return view('floors.index', compact('floors'));
    }

    public function create(): View
    {
        $this->authorize('create', Floor::class);

        return view('floors.create');
    }

    public function store(StoreFloorRequest $request): RedirectResponse
    {
        $this->authorize('create', Floor::class);

        $data = $request->validated();
        $data['branch_id'] = session('current_branch_id');

        $floor = $this->service->create($data);

        return redirect()->route('rooms.floors.show', $floor)
            ->with('success', 'Tạo tầng thành công.');
    }

    public function show(Floor $floor): View
    {
        $this->authorize('view', $floor);

        $floor->load('rooms.roomType');

        return view('floors.show', compact('floor'));
    }

    public function edit(Floor $floor): View
    {
        $this->authorize('update', $floor);

        return view('floors.edit', compact('floor'));
    }

    public function update(UpdateFloorRequest $request, Floor $floor): RedirectResponse
    {
        $this->authorize('update', $floor);

        $this->service->update($floor, $request->validated());

        return redirect()->route('rooms.floors.show', $floor)
            ->with('success', 'Cập nhật tầng thành công.');
    }

    public function destroy(Floor $floor): RedirectResponse
    {
        $this->authorize('delete', $floor);

        $this->service->delete($floor);

        return redirect()->route('rooms.floors.index')
            ->with('success', 'Xóa tầng thành công.');
    }

    public function map(Request $request): View
    {
        $this->authorize('viewAny', Floor::class);

        $branchId = session('current_branch_id');
        $floors = $this->service->getFloorsWithRooms($branchId, $request->integer('floor_id') ?: null);

        $statusColors = [
            RoomStatus::Available->value => 'success',
            RoomStatus::Occupied->value => 'danger',
            RoomStatus::Reserved->value => 'warning',
            RoomStatus::Maintenance->value => 'secondary',
            RoomStatus::Cleaning->value => 'info',
        ];

        return view('floors.map', compact('floors', 'statusColors'));
    }
}
