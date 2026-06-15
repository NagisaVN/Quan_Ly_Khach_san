<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\StoreUserRequest;
use App\Http\Requests\System\UpdateUserRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $users = $this->service->paginate($request->only('search'));

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->pluck('name');

        return view('users.create', compact('companies', 'branches', 'roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = $this->service->create($request->validated());

        return redirect()->route('system.users.show', $user)
            ->with('success', 'Tạo người dùng thành công.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load(['company', 'branches', 'roles']);

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->pluck('name');

        return view('users.edit', compact('user', 'companies', 'branches', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->service->update($user, $request->validated());

        return redirect()->route('system.users.show', $user)
            ->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->service->delete($user);

        return redirect()->route('system.users.index')
            ->with('success', 'Xóa người dùng thành công.');
    }
}
