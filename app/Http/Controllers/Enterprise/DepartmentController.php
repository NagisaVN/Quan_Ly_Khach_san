<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('enterprise.view'), 403);
        $departments = Department::where('branch_id', session('current_branch_id'))->paginate(15);

        return view('enterprise.departments.index', compact('departments'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);

        return view('enterprise.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('enterprise.create'), 403);
        $data = $request->validate(['name' => 'required', 'code' => 'nullable', 'description' => 'nullable']);
        $data['branch_id'] = session('current_branch_id');
        $dept = Department::create($data);

        return redirect()->route('enterprise.departments.show', $dept)->with('success', 'Tạo phòng ban');
    }

    public function show(Department $department): View
    {
        abort_unless(auth()->user()->can('enterprise.view'), 403);

        return view('enterprise.departments.show', compact('department'));
    }

    public function edit(Department $department): View
    {
        abort_unless(auth()->user()->can('enterprise.update'), 403);

        return view('enterprise.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        abort_unless(auth()->user()->can('enterprise.update'), 403);
        $department->update($request->validate(['name' => 'required', 'code' => 'nullable', 'is_active' => 'boolean']));

        return redirect()->route('enterprise.departments.show', $department)->with('success', 'Cập nhật');
    }

    public function destroy(Department $department): RedirectResponse
    {
        abort_unless(auth()->user()->can('enterprise.delete'), 403);
        $department->delete();

        return redirect()->route('enterprise.departments.index')->with('success', 'Đã xóa');
    }
}
