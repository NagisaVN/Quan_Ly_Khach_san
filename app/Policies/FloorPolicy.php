<?php

namespace App\Policies;

use App\Models\Floor;
use App\Models\User;

class FloorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('rooms.view');
    }

    public function view(User $user, Floor $model): bool
    {
        return $user->can('rooms.view');
    }

    public function create(User $user): bool
    {
        return $user->can('rooms.create');
    }

    public function update(User $user, Floor $model): bool
    {
        return $user->can('rooms.update');
    }

    public function delete(User $user, Floor $model): bool
    {
        return $user->can('rooms.delete');
    }
}
