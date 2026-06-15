<?php

namespace App\Http\Controllers\Rooms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rooms\StoreRoomTypeRequest;
use App\Http\Requests\Rooms\UpdateRoomTypeRequest;
use App\Models\RoomType;
use App\Services\RoomTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function __construct(private RoomTypeService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', RoomType::class);

        $roomTypes = $this->service->paginate($request->only('search'));

        return view('room-types.index', compact('roomTypes'));
    }

    public function create(): View
    {
        $this->authorize('create', RoomType::class);

        return view('room-types.create');
    }

    public function store(StoreRoomTypeRequest $request): RedirectResponse
    {
        $this->authorize('create', RoomType::class);

        $data = $request->validated();
        $data['company_id'] = auth()->user()->company_id;

        $roomType = $this->service->create($data);

        return redirect()->route('rooms.room-types.show', $roomType)
            ->with('success', 'Tạo loại phòng thành công.');
    }

    public function show(RoomType $roomType): View
    {
        $this->authorize('view', $roomType);

        return view('room-types.show', compact('roomType'));
    }

    public function edit(RoomType $roomType): View
    {
        $this->authorize('update', $roomType);

        return view('room-types.edit', compact('roomType'));
    }

    public function update(UpdateRoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $this->authorize('update', $roomType);

        $this->service->update($roomType, $request->validated());

        return redirect()->route('rooms.room-types.show', $roomType)
            ->with('success', 'Cập nhật loại phòng thành công.');
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        $this->authorize('delete', $roomType);

        $this->service->delete($roomType);

        return redirect()->route('rooms.room-types.index')
            ->with('success', 'Xóa loại phòng thành công.');
    }
}
