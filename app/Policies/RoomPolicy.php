<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('rooms.view');
    }

    public function view(User $user, Room $model): bool
    {
        return $user->can('rooms.view');
    }

    public function create(User $user): bool
    {
        return $user->can('rooms.create');
    }

    public function update(User $user, Room $model): bool
    {
        return $user->can('rooms.update');
    }

    public function delete(User $user, Room $model): bool
    {
        return $user->can('rooms.delete');
    }
}
