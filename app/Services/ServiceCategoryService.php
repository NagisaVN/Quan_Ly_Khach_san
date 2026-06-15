<?php

namespace App\Services;

use App\Models\ServiceCategory;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceCategoryService
{
    public function __construct(private ServiceCategoryRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): ServiceCategory
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): ServiceCategory
    {
        return $this->repository->create($data);
    }

    public function update(ServiceCategory $model, array $data): ServiceCategory
    {
        return $this->repository->update($model, $data);
    }

    public function delete(ServiceCategory $model): bool
    {
        return $this->repository->delete($model);
    }

    public function all(array $filters = [])
    {
        return $this->repository->all($filters);
    }
}
