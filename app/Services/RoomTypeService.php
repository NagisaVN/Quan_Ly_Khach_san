<?php

namespace App\Services;

use App\Models\RoomType;
use App\Repositories\Contracts\RoomTypeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoomTypeService
{
    public function __construct(private RoomTypeRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): RoomType
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): RoomType
    {
        return $this->repository->create($data);
    }

    public function update(RoomType $model, array $data): RoomType
    {
        return $this->repository->update($model, $data);
    }

    public function delete(RoomType $model): bool
    {
        return $this->repository->delete($model);
    }

    public function all(array $filters = [])
    {
        return $this->repository->all($filters);
    }
}
