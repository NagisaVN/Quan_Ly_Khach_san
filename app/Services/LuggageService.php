<?php

namespace App\Services;

use App\Models\Luggage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class LuggageService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Luggage::query()->with(['customer', 'booking']);

        if (!empty($filters['search'])) {
            $query->where('tag_code', 'like', '%' . $filters['search'] . '%')
                ->orWhereHas('customer', fn ($q) => $q->where('first_name', 'like', '%' . $filters['search'] . '%'));
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function create(array $data): Luggage
    {
        $data['tag_code'] = 'TAG-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $data['stored_at'] = now();
        $data['status'] = 'stored';

        return Luggage::create($data);
    }

    public function update(Luggage $luggage, array $data): Luggage
    {
        if (isset($data['status']) && $data['status'] === 'retrieved' && !isset($data['retrieved_at'])) {
            $data['retrieved_at'] = now();
        }

        $luggage->update($data);

        return $luggage;
    }

    public function delete(Luggage $luggage): bool
    {
        return $luggage->delete();
    }
}
