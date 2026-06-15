<?php

namespace App\Repositories\Eloquent;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BookingRepository implements BookingRepositoryInterface
{
    public function findOrFail(int $id): Booking
    {
        return Booking::with(['customer', 'bookingRooms.room', 'invoices'])->findOrFail($id);
    }

    public function findById(int $id): Booking
    {
        return Booking::with([
            'customer',
            'bookingRooms.room.roomType',
            'bookingRooms.room.floor',
            'invoices.payments',
            'serviceItems.service',
            'histories.user',
        ])->findOrFail($id);
    }

    public function paginateForBranch(int $branchId, array $filters = []): LengthAwarePaginator
    {
        $query = Booking::query()
            ->with(['customer', 'bookingRooms.room'])
            ->where('branch_id', $branchId)
            ->orderByDesc('created_at');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(15)->withQueryString();
    }

    public function getAvailableRooms(int $branchId, Carbon $checkIn, Carbon $checkOut, ?int $roomTypeId = null, ?array $roomIds = null): Collection
    {
        $query = Room::query()->where('branch_id', $branchId)->where('is_active', true)
            ->where('status', '!=', RoomStatus::Maintenance->value)->with(['roomType', 'floor']);
        if ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        }
        if ($roomIds) {
            $query->whereIn('id', $roomIds);
        }

        return $query->get()->filter(fn (Room $room) => ! $this->hasOverlap($room->id, $checkIn, $checkOut));
    }

    public function hasOverlap(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null): bool
    {
        return BookingRoom::query()->where('room_id', $roomId)
            ->where('check_in_date', '<', $checkOut->toDateString())
            ->where('check_out_date', '>', $checkIn->toDateString())
            ->whereHas('booking', function ($q) use ($excludeBookingId) {
                $q->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::CheckedOut->value]);
                if ($excludeBookingId) {
                    $q->where('id', '!=', $excludeBookingId);
                }
            })->exists();
    }

    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function update(Booking $booking, array $data): Booking
    {
        $booking->update($data);

        return $booking->fresh();
    }
}
