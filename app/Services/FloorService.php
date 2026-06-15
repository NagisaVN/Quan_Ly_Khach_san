<?php

namespace App\Services;

use App\Models\Floor;
use App\Repositories\Contracts\FloorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FloorService
{
    public function __construct(private FloorRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Floor
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Floor
    {
        return $this->repository->create($data);
    }

    public function update(Floor $model, array $data): Floor
    {
        return $this->repository->update($model, $data);
    }

    public function delete(Floor $model): bool
    {
        return $this->repository->delete($model);
    }

    public function getFloorsWithRooms(?int $branchId, ?int $floorId = null): Collection
    {
        return $this->repository->getFloorsWithRooms($branchId, $floorId);
    }
}
