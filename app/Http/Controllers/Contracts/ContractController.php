<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Contract;
use App\Models\Supplier;
use App\Services\ContractService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function __construct(private ContractService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Contract::class);
        $contracts = $this->service->paginate($request->only('search', 'status', 'supplier_id'));

        return view('contracts.index', compact('contracts'));
    }

    public function create(): View
    {
        $this->authorize('create', Contract::class);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('contracts.create', compact('suppliers'));
    }

    public function store(StoreContractRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['company_id'] = auth()->user()->company_id;
        $data['branch_id'] = session('current_branch_id');
        $contract = $this->service->create($data);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Tạo hợp đồng thành công.');
    }

    public function show(Contract $contract): View
    {
        $this->authorize('view', $contract);
        $contract->load('supplier');

        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract): View
    {
        $this->authorize('update', $contract);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('contracts.edit', compact('contract', 'suppliers'));
    }

    public function update(UpdateContractRequest $request, Contract $contract): RedirectResponse
    {
        $this->authorize('update', $contract);
        $this->service->update($contract, $request->validated());

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Cập nhật hợp đồng thành công.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $this->authorize('delete', $contract);
        $this->service->delete($contract);

        return redirect()->route('contracts.index')
            ->with('success', 'Xóa hợp đồng thành công.');
    }
}
