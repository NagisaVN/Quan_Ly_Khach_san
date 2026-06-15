<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface
{
    public function findOrFail(int $id): Booking;

    public function findById(int $id): Booking;

    public function paginateForBranch(int $branchId, array $filters = []): LengthAwarePaginator;

    public function getAvailableRooms(int $branchId, Carbon $checkIn, Carbon $checkOut, ?int $roomTypeId = null, ?array $roomIds = null): Collection;

    public function hasOverlap(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null): bool;

    public function create(array $data): Booking;

    public function update(Booking $booking, array $data): Booking;
}
