<?php

namespace App\Policies;

use App\Models\Luggage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LuggagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('luggage.view');
    }

    public function view(User $user, Luggage $luggage): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('luggage.view') && $luggage->branch_id == $user->current_branch_id;
    }

    public function create(User $user): bool
    {
        return $user->can('luggage.create');
    }

    public function update(User $user, Luggage $luggage): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('luggage.update') && $luggage->branch_id == $user->current_branch_id;
    }

    public function delete(User $user, Luggage $luggage): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('luggage.delete') && $luggage->branch_id == $user->current_branch_id;
    }
}
