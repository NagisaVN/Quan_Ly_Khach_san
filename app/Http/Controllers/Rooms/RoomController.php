<?php

namespace App\Http\Controllers\Rooms;

use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rooms\StoreRoomRequest;
use App\Http\Requests\Rooms\UpdateRoomRequest;
use App\Models\Amenity;
use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __construct(private RoomService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Room::class);

        $rooms = $this->service->paginate($request->only('search'));

        return view('rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        $this->authorize('create', Room::class);

        $floors = Floor::where('branch_id', session('current_branch_id'))->where('is_active', true)->orderBy('floor_number')->get();
        $roomTypes = RoomType::where('company_id', auth()->user()->company_id)->where('is_active', true)->orderBy('name')->get();
        $amenities = Amenity::where('company_id', auth()->user()->company_id)->where('is_active', true)->orderBy('name')->get();
        $statuses = RoomStatus::cases();

        return view('rooms.create', compact('floors', 'roomTypes', 'amenities', 'statuses'));
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $this->authorize('create', Room::class);

        $data = $request->validated();
        $data['branch_id'] = session('current_branch_id');
        $amenityIds = $data['amenity_ids'] ?? [];
        unset($data['amenity_ids']);

        $room = $this->service->create($data, $amenityIds);

        return redirect()->route('rooms.rooms.show', $room)
            ->with('success', 'Tạo phòng thành công.');
    }

    public function show(Room $room): View
    {
        $this->authorize('view', $room);

        $room->load(['floor', 'roomType', 'amenities']);

        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $this->authorize('update', $room);

        $floors = Floor::where('branch_id', session('current_branch_id'))->where('is_active', true)->orderBy('floor_number')->get();
        $roomTypes = RoomType::where('company_id', auth()->user()->company_id)->where('is_active', true)->orderBy('name')->get();
        $amenities = Amenity::where('company_id', auth()->user()->company_id)->where('is_active', true)->orderBy('name')->get();
        $statuses = RoomStatus::cases();

        return view('rooms.edit', compact('room', 'floors', 'roomTypes', 'amenities', 'statuses'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        $data = $request->validated();
        $amenityIds = $data['amenity_ids'] ?? [];
        unset($data['amenity_ids']);

        $this->service->update($room, $data, $amenityIds);

        return redirect()->route('rooms.rooms.show', $room)
            ->with('success', 'Cập nhật phòng thành công.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);

        $this->service->delete($room);

        return redirect()->route('rooms.rooms.index')
            ->with('success', 'Xóa phòng thành công.');
    }
}
