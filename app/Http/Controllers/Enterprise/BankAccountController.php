<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('enterprise.view'), 403);
        $bankAccounts = BankAccount::where('company_id', auth()->user()->company_id)->paginate(15);

        return view('enterprise.bank-accounts.index', compact('bankAccounts'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);

        return view('enterprise.bank-accounts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);
        $data = $request->validate([
            'bank_name' => 'required',
            'account_number' => 'required',
            'account_holder' => 'required',
        ]);
        $data['company_id'] = auth()->user()->company_id;
        $data['branch_id'] = session('current_branch_id');
        $account = BankAccount::create($data);

        return redirect()->route('enterprise.bank-accounts.show', $account)->with('success', 'Đã tạo tài khoản');
    }

    public function show(BankAccount $bankAccount): View
    {
        abort_unless(auth()->user()->can('enterprise.view'), 403);

        return view('enterprise.bank-accounts.show', compact('bankAccount'));
    }
}
