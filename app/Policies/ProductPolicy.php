<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('inventory.view');
    }

    public function view(User $user, Product $product): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('inventory.view') && $product->branch_id == $user->current_branch_id;
    }

    public function create(User $user): bool
    {
        return $user->can('inventory.create');
    }

    public function update(User $user, Product $product): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('inventory.update') && $product->branch_id == $user->current_branch_id;
    }

    public function delete(User $user, Product $product): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('inventory.delete') && $product->branch_id == $user->current_branch_id;
    }
}
