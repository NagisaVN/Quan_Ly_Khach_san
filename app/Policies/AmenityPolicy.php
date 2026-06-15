<?php

namespace App\Policies;

use App\Models\Amenity;
use App\Models\User;

class AmenityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('rooms.view');
    }

    public function view(User $user, Amenity $model): bool
    {
        return $user->can('rooms.view');
    }

    public function create(User $user): bool
    {
        return $user->can('rooms.create');
    }

    public function update(User $user, Amenity $model): bool
    {
        return $user->can('rooms.update');
    }

    public function delete(User $user, Amenity $model): bool
    {
        return $user->can('rooms.delete');
    }
}
