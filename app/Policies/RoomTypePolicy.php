<?php

namespace App\Policies;

use App\Models\RoomType;
use App\Models\User;

class RoomTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('rooms.view');
    }

    public function view(User $user, RoomType $model): bool
    {
        return $user->can('rooms.view');
    }

    public function create(User $user): bool
    {
        return $user->can('rooms.create');
    }

    public function update(User $user, RoomType $model): bool
    {
        return $user->can('rooms.update');
    }

    public function delete(User $user, RoomType $model): bool
    {
        return $user->can('rooms.delete');
    }
}
