<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enterprise\StoreBranchRequest;
use App\Http\Requests\Enterprise\UpdateBranchRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Services\BranchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(private BranchService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Branch::class);

        $branches = $this->service->paginate($request->only('search'));

        return view('branches.index', compact('branches'));
    }

    public function create(): View
    {
        $this->authorize('create', Branch::class);

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('branches.create', compact('companies'));
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        $this->authorize('create', Branch::class);

        $branch = $this->service->create($request->validated());

        return redirect()->route('enterprise.branches.show', $branch)
            ->with('success', 'Tạo chi nhánh thành công.');
    }

    public function show(Branch $branch): View
    {
        $this->authorize('view', $branch);

        $branch->load('company');

        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch): View
    {
        $this->authorize('update', $branch);

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('branches.edit', compact('branch', 'companies'));
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $this->authorize('update', $branch);

        $this->service->update($branch, $request->validated());

        return redirect()->route('enterprise.branches.show', $branch)
            ->with('success', 'Cập nhật chi nhánh thành công.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $this->authorize('delete', $branch);

        $this->service->delete($branch);

        return redirect()->route('enterprise.branches.index')
            ->with('success', 'Xóa chi nhánh thành công.');
    }
}
