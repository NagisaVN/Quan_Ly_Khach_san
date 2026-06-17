<?php

namespace App\Services;

use App\Models\Contract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ContractService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Contract::query()->with('supplier');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%')
                ->orWhere('contract_number', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function findOrFail(int $id): Contract
    {
        return Contract::findOrFail($id);
    }

    public function create(array $data): Contract
    {
        if (!isset($data['contract_number'])) {
            $data['contract_number'] = 'CT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        }

        $data['status'] = 'active';

        return Contract::create($data);
    }

    public function update(Contract $contract, array $data): Contract
    {
        $contract->update($data);

        return $contract;
    }

    public function delete(Contract $contract): bool
    {
        return $contract->delete();
    }

    public function getActiveContracts(int $companyId): array
    {
        return Contract::query()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->orderBy('end_date')
            ->get()
            ->toArray();
    }

    public function getExpiringContracts(int $companyId, int $daysAhead = 30): array
    {
        return Contract::query()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays($daysAhead)])
            ->orderBy('end_date')
            ->get()
            ->toArray();
    }
}
