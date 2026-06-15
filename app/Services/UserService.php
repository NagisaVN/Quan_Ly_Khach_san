<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): User
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): User
    {
        $role = $data['role'];
        $branchIds = $data['branch_ids'] ?? [];
        unset($data['role'], $data['branch_ids']);

        $data['password'] = Hash::make($data['password']);

        if (! empty($branchIds)) {
            $data['current_branch_id'] = $branchIds[0];
        }

        $user = $this->repository->create($data);
        $user->assignRole($role);

        if (! empty($branchIds)) {
            $sync = [];
            foreach ($branchIds as $i => $branchId) {
                $sync[$branchId] = ['is_default' => $i === 0];
            }
            $user->branches()->sync($sync);
        }

        return $user;
    }

    public function update(User $model, array $data): User
    {
        $role = $data['role'];
        $branchIds = $data['branch_ids'] ?? [];
        unset($data['role'], $data['branch_ids']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->repository->update($model, $data);
        $user->syncRoles([$role]);

        if (! empty($branchIds)) {
            $sync = [];
            foreach ($branchIds as $i => $branchId) {
                $sync[$branchId] = ['is_default' => $i === 0];
            }
            $user->branches()->sync($sync);
        }

        return $user;
    }

    public function delete(User $model): bool
    {
        return $this->repository->delete($model);
    }
}
