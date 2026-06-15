<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('enterprise.view'), 403);
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)->paginate(15);

        return view('enterprise.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);

        return view('enterprise.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);
        $data = $request->validate(['name' => 'required', 'code' => 'nullable', 'phone' => 'nullable', 'email' => 'nullable|email']);
        $data['company_id'] = auth()->user()->company_id;
        $supplier = Supplier::create($data);

        return redirect()->route('enterprise.suppliers.show', $supplier)->with('success', 'Tạo NCC');
    }

    public function show(Supplier $supplier): View
    {
        abort_unless(auth()->user()->can('enterprise.view'), 403);

        return view('enterprise.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        abort_unless(auth()->user()->can('enterprise.update'), 403);

        return view('enterprise.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        abort_unless(auth()->user()->can('enterprise.update'), 403);
        $supplier->update($request->validate(['name' => 'required', 'phone' => 'nullable', 'is_active' => 'boolean']));

        return redirect()->route('enterprise.suppliers.show', $supplier)->with('success', 'Cập nhật');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        abort_unless(auth()->user()->can('enterprise.delete'), 403);
        $supplier->delete();

        return redirect()->route('enterprise.suppliers.index')->with('success', 'Đã xóa');
    }
}
