<?php

namespace App\Repositories\Eloquent;

use App\Models\Amenity;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class AmenityRepository extends BaseRepository implements AmenityRepositoryInterface
{
    public function __construct(Amenity $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if ($companyId = $this->currentCompanyId()) {
            $query->where('company_id', $companyId);
        }
        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search);
            });
        }

        return $query;
    }
}
