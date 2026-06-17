<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MaintenanceRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('maintenance.view');
    }

    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('maintenance.view') && $maintenanceRequest->branch_id == $user->current_branch_id;
    }

    public function create(User $user): bool
    {
        return $user->can('maintenance.create');
    }

    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('maintenance.update') && $maintenanceRequest->branch_id == $user->current_branch_id;
    }

    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('maintenance.delete') && $maintenanceRequest->branch_id == $user->current_branch_id;
    }
}
