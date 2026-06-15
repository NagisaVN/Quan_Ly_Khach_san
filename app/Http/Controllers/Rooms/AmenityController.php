<?php

namespace App\Http\Controllers\Rooms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rooms\StoreAmenityRequest;
use App\Http\Requests\Rooms\UpdateAmenityRequest;
use App\Models\Amenity;
use App\Services\AmenityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AmenityController extends Controller
{
    public function __construct(private AmenityService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Amenity::class);

        $amenities = $this->service->paginate($request->only('search'));

        return view('amenities.index', compact('amenities'));
    }

    public function create(): View
    {
        $this->authorize('create', Amenity::class);

        return view('amenities.create');
    }

    public function store(StoreAmenityRequest $request): RedirectResponse
    {
        $this->authorize('create', Amenity::class);

        $data = $request->validated();
        $data['company_id'] = auth()->user()->company_id;

        $amenity = $this->service->create($data);

        return redirect()->route('rooms.amenities.show', $amenity)
            ->with('success', 'Tạo tiện ích thành công.');
    }

    public function show(Amenity $amenity): View
    {
        $this->authorize('view', $amenity);

        return view('amenities.show', compact('amenity'));
    }

    public function edit(Amenity $amenity): View
    {
        $this->authorize('update', $amenity);

        return view('amenities.edit', compact('amenity'));
    }

    public function update(UpdateAmenityRequest $request, Amenity $amenity): RedirectResponse
    {
        $this->authorize('update', $amenity);

        $this->service->update($amenity, $request->validated());

        return redirect()->route('rooms.amenities.show', $amenity)
            ->with('success', 'Cập nhật tiện ích thành công.');
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        $this->authorize('delete', $amenity);

        $this->service->delete($amenity);

        return redirect()->route('rooms.amenities.index')
            ->with('success', 'Xóa tiện ích thành công.');
    }
}
