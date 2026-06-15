<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('enterprise.view');
    }

    public function view(User $user, Branch $model): bool
    {
        return $user->can('enterprise.view');
    }

    public function create(User $user): bool
    {
        return $user->can('enterprise.create');
    }

    public function update(User $user, Branch $model): bool
    {
        return $user->can('enterprise.update');
    }

    public function delete(User $user, Branch $model): bool
    {
        return $user->can('enterprise.delete');
    }
}
