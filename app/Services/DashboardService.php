<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;

class DashboardService
{
    public function getKpis(?int $branchId): array
    {
        $roomQ = Room::query();
        $bookQ = Booking::query();
        $payQ = Payment::query();

        if ($branchId) {
            $roomQ->where('branch_id', $branchId);
            $bookQ->where('branch_id', $branchId);
            $payQ->where('branch_id', $branchId);
        }

        return [
            'available_rooms' => (clone $roomQ)->where('status', RoomStatus::Available->value)->count(),
            'total_rooms' => (clone $roomQ)->where('is_active', true)->count(),
            'check_ins_today' => (clone $bookQ)->whereDate('check_in_date', today())->where('status', '!=', BookingStatus::Cancelled->value)->count(),
            'check_outs_today' => (clone $bookQ)->whereDate('check_out_date', today())->where('status', '!=', BookingStatus::Cancelled->value)->count(),
            'revenue_today' => (float) (clone $payQ)->whereDate('paid_at', today())->where('status', 'completed')->sum('amount'),
            'revenue_week' => (float) (clone $payQ)->whereBetween('paid_at', [today()->subDays(7), today()])->where('status', 'completed')->sum('amount'),
            'occupancy_rate' => $this->todayOccupancy($branchId),
        ];
    }

    public function getRevenueChart(?int $branchId): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d/m');
            $query = Payment::query()->whereDate('paid_at', $date)->where('status', 'completed');
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            $data[] = (float) $query->sum('amount');
        }

        return compact('labels', 'data');
    }

    public function getOccupancyChart(?int $branchId): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d/m');
            $data[] = $this->occupancyForDate($date, $branchId);
        }

        return compact('labels', 'data');
    }

    public function getRecentBookings(?int $branchId, int $limit = 5)
    {
        $query = Booking::with(['customer', 'bookingRooms.room'])
            ->latest()
            ->limit($limit);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getBookingStatusChart(?int $branchId): array
    {
        $statuses = BookingStatus::cases();
        $labels = [];
        $data = [];

        foreach ($statuses as $status) {
            $query = Booking::query()->where('status', $status->value);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            $labels[] = match ($status) {
                BookingStatus::Pending => 'Chờ xử lý',
                BookingStatus::Confirmed => 'Đã xác nhận',
                BookingStatus::CheckedIn => 'Đang ở',
                BookingStatus::CheckedOut => 'Đã trả',
                BookingStatus::Cancelled => 'Đã hủy',
                BookingStatus::NoShow => 'No-show',
            };
            $data[] = $query->count();
        }

        return compact('labels', 'data');
    }

    private function todayOccupancy(?int $branchId): float
    {
        return $this->occupancyForDate(Carbon::today(), $branchId);
    }

    private function occupancyForDate(Carbon $date, ?int $branchId): float
    {
        $roomQuery = Room::query()->where('is_active', true);
        if ($branchId) {
            $roomQuery->where('branch_id', $branchId);
        }
        $totalRooms = (clone $roomQuery)->count();

        if ($totalRooms === 0) {
            return 0;
        }

        $occupiedQuery = BookingRoom::query()
            ->where('check_in_date', '<=', $date->toDateString())
            ->where('check_out_date', '>', $date->toDateString())
            ->whereHas('booking', function ($q) use ($branchId) {
                $q->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::CheckedOut->value]);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            });

        $occupied = $occupiedQuery->distinct('room_id')->count('room_id');

        return round(($occupied / $totalRooms) * 100, 1);
    }
}
