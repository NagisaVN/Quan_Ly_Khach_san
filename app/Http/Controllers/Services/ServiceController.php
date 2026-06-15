<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(private ServiceService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Service::class);

        $services = $this->service->paginate($request->only('search'));

        return view('items.index', compact('services'));
    }

    public function create(): View
    {
        $this->authorize('create', Service::class);

        $categories = ServiceCategory::where('branch_id', session('current_branch_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('items.create', compact('categories'));
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Service::class);

        $data = $request->validated();
        $data['branch_id'] = session('current_branch_id');

        $service = $this->service->create($data);

        return redirect()->route('services.items.show', $service)
            ->with('success', 'Tạo dịch vụ thành công.');
    }

    public function show(Service $item): View
    {
        $this->authorize('view', $item);

        $item->load('category');

        return view('items.show', ['service' => $item]);
    }

    public function edit(Service $item): View
    {
        $this->authorize('update', $item);

        $categories = ServiceCategory::where('branch_id', session('current_branch_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('items.edit', ['service' => $item, 'categories' => $categories]);
    }

    public function update(UpdateServiceRequest $request, Service $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $this->service->update($item, $request->validated());

        return redirect()->route('services.items.show', $item)
            ->with('success', 'Cập nhật dịch vụ thành công.');
    }

    public function destroy(Service $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        $this->service->delete($item);

        return redirect()->route('services.items.index')
            ->with('success', 'Xóa dịch vụ thành công.');
    }
}
