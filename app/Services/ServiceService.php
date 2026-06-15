<?php

namespace App\Services;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceService
{
    public function __construct(private ServiceRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Service
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Service
    {
        return $this->repository->create($data);
    }

    public function update(Service $model, array $data): Service
    {
        return $this->repository->update($model, $data);
    }

    public function delete(Service $model): bool
    {
        return $this->repository->delete($model);
    }

    public function all(array $filters = [])
    {
        return $this->repository->all($filters);
    }
}
