<?php

namespace App\Services;

use App\Models\Room;
use App\Repositories\Contracts\RoomRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoomService
{
    public function __construct(private RoomRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Room
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, array $amenityIds = []): Room
    {
        $room = $this->repository->create($data);

        if (! empty($amenityIds)) {
            $room->amenities()->sync($amenityIds);
        }

        return $room->load(['floor', 'roomType', 'amenities']);
    }

    public function update(Room $model, array $data, array $amenityIds = []): Room
    {
        $room = $this->repository->update($model, $data);
        $room->amenities()->sync($amenityIds);

        return $room->load(['floor', 'roomType', 'amenities']);
    }

    public function delete(Room $model): bool
    {
        return $this->repository->delete($model);
    }
}
