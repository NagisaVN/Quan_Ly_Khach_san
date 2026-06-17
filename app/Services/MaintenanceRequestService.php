<?php

namespace App\Services;

use App\Models\MaintenanceRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MaintenanceRequestService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = MaintenanceRequest::query()->with('room');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%')
                ->orWhere('description', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function findOrFail(int $id): MaintenanceRequest
    {
        return MaintenanceRequest::findOrFail($id);
    }

    public function create(array $data): MaintenanceRequest
    {
        $data['status'] = 'open';
        $data['reported_at'] = now();

        return MaintenanceRequest::create($data);
    }

    public function update(MaintenanceRequest $request, array $data): MaintenanceRequest
    {
        if (isset($data['status'])) {
            if ($data['status'] === 'in-progress' && !isset($data['started_at'])) {
                $data['started_at'] = now();
            }

            if ($data['status'] === 'completed' && !isset($data['completed_at'])) {
                $data['completed_at'] = now();
            }
        }

        $request->update($data);

        return $request;
    }

    public function delete(MaintenanceRequest $request): bool
    {
        return $request->delete();
    }

    public function getOpenRequests(int $branchId): array
    {
        return MaintenanceRequest::query()
            ->where('branch_id', $branchId)
            ->whereIn('status', ['open', 'in-progress'])
            ->orderByDesc('priority')
            ->orderBy('reported_at')
            ->get()
            ->toArray();
    }
}
