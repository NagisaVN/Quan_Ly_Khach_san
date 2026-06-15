<?php

namespace App\Services;

use App\Models\Amenity;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AmenityService
{
    public function __construct(private AmenityRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Amenity
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Amenity
    {
        return $this->repository->create($data);
    }

    public function update(Amenity $model, array $data): Amenity
    {
        return $this->repository->update($model, $data);
    }

    public function delete(Amenity $model): bool
    {
        return $this->repository->delete($model);
    }

    public function all(array $filters = [])
    {
        return $this->repository->all($filters);
    }
}
