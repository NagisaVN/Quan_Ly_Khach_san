<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('contracts.view'), 403);
        $contracts = Contract::query()
            ->where('company_id', auth()->user()->company_id)
            ->orderByDesc('id')->paginate(15);

        return view('contracts.index', compact('contracts'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('contracts.create'), 403);

        return view('contracts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('contracts.create'), 403);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_value' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $data['company_id'] = auth()->user()->company_id;
        $data['branch_id'] = session('current_branch_id');
        $data['contract_number'] = 'CT-'.strtoupper(Str::random(8));
        $data['status'] = 'active';
        $contract = Contract::create($data);

        return redirect()->route('contracts.show', $contract)->with('success', 'Tạo hợp đồng');
    }

    public function show(Contract $contract): View
    {
        abort_unless(auth()->user()->can('contracts.view'), 403);

        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract): View
    {
        abort_unless(auth()->user()->can('contracts.update'), 403);

        return view('contracts.edit', compact('contract'));
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        abort_unless(auth()->user()->can('contracts.update'), 403);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,expired,cancelled',
            'total_value' => 'nullable|numeric|min:0',
        ]);
        $contract->update($data);

        return redirect()->route('contracts.show', $contract)->with('success', 'Cập nhật hợp đồng');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        abort_unless(auth()->user()->can('contracts.delete'), 403);
        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Đã xóa');
    }
}
