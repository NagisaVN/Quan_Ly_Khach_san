<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface FloorRepositoryInterface extends BaseRepositoryInterface
{
    public function getFloorsWithRooms(?int $branchId, ?int $floorId = null): Collection;
}
