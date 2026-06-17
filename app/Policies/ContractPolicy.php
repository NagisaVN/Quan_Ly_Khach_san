<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('contracts.view');
    }

    public function view(User $user, Contract $contract): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('contracts.view') && $contract->branch_id == $user->current_branch_id;
    }

    public function create(User $user): bool
    {
        return $user->can('contracts.create');
    }

    public function update(User $user, Contract $contract): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('contracts.update') && $contract->branch_id == $user->current_branch_id;
    }

    public function delete(User $user, Contract $contract): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('contracts.delete') && $contract->branch_id == $user->current_branch_id;
    }
}
