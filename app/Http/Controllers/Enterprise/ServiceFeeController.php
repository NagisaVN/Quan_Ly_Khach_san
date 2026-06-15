<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use App\Models\ServiceFee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceFeeController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('enterprise.view'), 403);
        $serviceFees = ServiceFee::where('company_id', auth()->user()->company_id)->paginate(15);

        return view('enterprise.service-fees.index', compact('serviceFees'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);

        return view('enterprise.service-fees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);
        $data = $request->validate(['name' => 'required', 'code' => 'required', 'value' => 'required|numeric']);
        $data['company_id'] = auth()->user()->company_id;
        $data['branch_id'] = session('current_branch_id');
        $fee = ServiceFee::create($data);

        return redirect()->route('enterprise.service-fees.show', $fee)->with('success', 'Đã tạo phí');
    }

    public function show(ServiceFee $serviceFee): View
    {
        abort_unless(auth()->user()->can('enterprise.view'), 403);

        return view('enterprise.service-fees.show', compact('serviceFee'));
    }
}
