<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyService
{
    public function __construct(private CompanyRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Company
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Company
    {
        return $this->repository->create($data);
    }

    public function update(Company $model, array $data): Company
    {
        return $this->repository->update($model, $data);
    }

    public function delete(Company $model): bool
    {
        return $this->repository->delete($model);
    }

    public function all(array $filters = [])
    {
        return $this->repository->all($filters);
    }
}
