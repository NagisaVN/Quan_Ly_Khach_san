<?php

namespace App\Policies;

use App\Models\User;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('system.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('system.view');
    }

    public function create(User $user): bool
    {
        return $user->can('system.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('system.update');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('system.delete');
    }
}
