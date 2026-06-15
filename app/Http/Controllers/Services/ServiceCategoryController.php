<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\StoreServiceCategoryRequest;
use App\Http\Requests\Services\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use App\Services\ServiceCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function __construct(private ServiceCategoryService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', ServiceCategory::class);

        $serviceCategories = $this->service->paginate($request->only('search'));

        return view('categories.index', compact('serviceCategories'));
    }

    public function create(): View
    {
        $this->authorize('create', ServiceCategory::class);

        return view('categories.create');
    }

    public function store(StoreServiceCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', ServiceCategory::class);

        $data = $request->validated();
        $data['branch_id'] = session('current_branch_id');

        $serviceCategory = $this->service->create($data);

        return redirect()->route('services.categories.show', $serviceCategory)
            ->with('success', 'Tạo danh mục dịch vụ thành công.');
    }

    public function show(ServiceCategory $category): View
    {
        $this->authorize('view', $category);

        $category->load('services');

        return view('categories.show', ['serviceCategory' => $category]);
    }

    public function edit(ServiceCategory $category): View
    {
        $this->authorize('update', $category);

        return view('categories.edit', ['serviceCategory' => $category]);
    }

    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $this->service->update($category, $request->validated());

        return redirect()->route('services.categories.show', $category)
            ->with('success', 'Cập nhật danh mục dịch vụ thành công.');
    }

    public function destroy(ServiceCategory $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $this->service->delete($category);

        return redirect()->route('services.categories.index')
            ->with('success', 'Xóa danh mục dịch vụ thành công.');
    }
}
