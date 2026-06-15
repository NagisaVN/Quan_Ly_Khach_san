<?php

namespace App\Repositories\Eloquent;

use App\Models\Floor;
use App\Repositories\Contracts\FloorRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FloorRepository extends BaseRepository implements FloorRepositoryInterface
{
    public function __construct(Floor $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if ($branchId = $this->currentBranchId()) {
            $query->where('branch_id', $branchId);
        }

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search);
            });
        }

        return $query;
    }

    public function getFloorsWithRooms(?int $branchId, ?int $floorId = null): Collection
    {
        $query = $this->query()
            ->with(['rooms' => fn ($q) => $q->with('roomType')->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('floor_number');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($floorId) {
            $query->where('id', $floorId);
        }

        return $query->get();
    }
}
