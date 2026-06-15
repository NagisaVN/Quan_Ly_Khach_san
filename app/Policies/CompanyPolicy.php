<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('enterprise.view');
    }

    public function view(User $user, Company $model): bool
    {
        return $user->can('enterprise.view');
    }

    public function create(User $user): bool
    {
        return $user->can('enterprise.create');
    }

    public function update(User $user, Company $model): bool
    {
        return $user->can('enterprise.update');
    }

    public function delete(User $user, Company $model): bool
    {
        return $user->can('enterprise.delete');
    }
}
