<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('enterprise.view'), 403);
        $taxes = Tax::where('company_id', auth()->user()->company_id)->paginate(15);

        return view('enterprise.taxes.index', compact('taxes'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);

        return view('enterprise.taxes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);
        $data = $request->validate(['name' => 'required', 'code' => 'required', 'rate' => 'required|numeric']);
        $data['company_id'] = auth()->user()->company_id;
        $tax = Tax::create($data);

        return redirect()->route('enterprise.taxes.show', $tax)->with('success', 'Đã tạo thuế');
    }

    public function show(Tax $tax): View
    {
        abort_unless(auth()->user()->can('enterprise.view'), 403);

        return view('enterprise.taxes.show', compact('tax'));
    }
}
