<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchSwitchController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $user = $request->user();

        if (! $user->branches()->where('branches.id', $request->integer('branch_id'))->exists()) {
            abort(403, 'Bạn không có quyền truy cập chi nhánh này.');
        }

        $branchId = $request->integer('branch_id');
        $request->session()->put('current_branch_id', $branchId);
        $user->update(['current_branch_id' => $branchId]);

        return redirect()->back()->with('success', 'Đã chuyển chi nhánh thành công.');
    }
}
