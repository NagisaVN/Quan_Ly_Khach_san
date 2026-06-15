<?php

namespace App\Services;

use App\Models\Branch;
use App\Repositories\Contracts\BranchRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BranchService
{
    public function __construct(private BranchRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Branch
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Branch
    {
        return $this->repository->create($data);
    }

    public function update(Branch $model, array $data): Branch
    {
        return $this->repository->update($model, $data);
    }

    public function delete(Branch $model): bool
    {
        return $this->repository->delete($model);
    }

    public function all(array $filters = [])
    {
        return $this->repository->all($filters);
    }
}
