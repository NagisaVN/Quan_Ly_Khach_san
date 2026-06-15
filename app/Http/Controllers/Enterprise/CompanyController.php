<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enterprise\StoreCompanyRequest;
use App\Http\Requests\Enterprise\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(private CompanyService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Company::class);

        $companies = $this->service->paginate($request->only('search'));

        return view('companies.index', compact('companies'));
    }

    public function create(): View
    {
        $this->authorize('create', Company::class);

        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $this->authorize('create', Company::class);

        $company = $this->service->create($request->validated());

        return redirect()->route('enterprise.companies.show', $company)
            ->with('success', 'Tạo công ty thành công.');
    }

    public function show(Company $company): View
    {
        $this->authorize('view', $company);

        $company->load('branches');

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        $this->authorize('update', $company);

        return view('companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $this->service->update($company, $request->validated());

        return redirect()->route('enterprise.companies.show', $company)
            ->with('success', 'Cập nhật công ty thành công.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $this->authorize('delete', $company);

        $this->service->delete($company);

        return redirect()->route('enterprise.companies.index')
            ->with('success', 'Xóa công ty thành công.');
    }
}
