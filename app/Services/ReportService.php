<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Models\BookingRoom;
use App\Models\BookingServiceItem;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function revenueReport(
        int $branchId,
        Carbon $fromDate,
        Carbon $toDate,
        string $groupBy = 'day'
    ): array {
        $payments = Payment::query()
            ->forBranch($branchId)
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$fromDate->startOfDay(), $toDate->endOfDay()])
            ->get();

        $grouped = [];
        $paymentsByMethod = [];

        foreach ($payments as $payment) {
            $key = $this->groupKey($payment->paid_at, $groupBy);
            $grouped[$key] = ($grouped[$key] ?? 0) + (float) $payment->amount;

            $method = $payment->payment_method->value;
            $paymentsByMethod[$method] = ($paymentsByMethod[$method] ?? 0) + (float) $payment->amount;
        }

        ksort($grouped);

        return [
            'labels' => array_keys($grouped),
            'revenue' => array_values($grouped),
            'payments_by_method' => $paymentsByMethod,
            'total' => array_sum($grouped),
        ];
    }

    public function occupancyReport(int $branchId, Carbon $fromDate, Carbon $toDate): array
    {
        $totalRooms = Room::query()
            ->forBranch($branchId)
            ->where('is_active', true)
            ->count();

        $days = max($fromDate->diffInDays($toDate) + 1, 1);
        $data = [];

        $cursor = $fromDate->copy();
        while ($cursor->lte($toDate)) {
            $occupied = BookingRoom::query()
                ->where('check_in_date', '<=', $cursor->toDateString())
                ->where('check_out_date', '>', $cursor->toDateString())
                ->whereHas('booking', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                        ->whereIn('status', [
                            BookingStatus::Confirmed->value,
                            BookingStatus::CheckedIn->value,
                        ]);
                })
                ->distinct('room_id')
                ->count('room_id');

            $rate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100, 2) : 0;

            $data[] = [
                'date' => $cursor->toDateString(),
                'occupied_rooms' => $occupied,
                'total_rooms' => $totalRooms,
                'rate_percent' => $rate,
            ];

            $cursor->addDay();
        }

        $totalOccupiedNights = array_sum(array_column($data, 'occupied_rooms'));
        $averageRate = $totalRooms > 0
            ? round(($totalOccupiedNights / ($totalRooms * $days)) * 100, 2)
            : 0;

        return [
            'data' => $data,
            'average_occupancy' => $averageRate,
        ];
    }

    public function topServicesReport(int $branchId, Carbon $fromDate, Carbon $toDate, int $limit = 10): array
    {
        $items = BookingServiceItem::query()
            ->select([
                'service_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_amount) as total_revenue'),
            ])
            ->whereBetween('service_date', [$fromDate->startOfDay(), $toDate->endOfDay()])
            ->whereHas('booking', fn ($q) => $q->where('branch_id', $branchId))
            ->with('service:id,name')
            ->groupBy('service_id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return [
            'labels' => $items->map(fn ($item) => $item->service?->name ?? 'N/A')->all(),
            'quantities' => $items->pluck('total_quantity')->map(fn ($v) => (int) $v)->all(),
            'revenue' => $items->pluck('total_revenue')->map(fn ($v) => (float) $v)->all(),
        ];
    }

    public function topCustomersReport(int $branchId, Carbon $fromDate, Carbon $toDate, int $limit = 10): array
    {
        $customers = Customer::query()
            ->select([
                'customers.id',
                'customers.first_name',
                'customers.last_name',
                'customers.code',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(bookings.total_amount) as total_spent'),
            ])
            ->join('bookings', 'bookings.customer_id', '=', 'customers.id')
            ->where('bookings.branch_id', $branchId)
            ->whereBetween('bookings.check_in_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->where('bookings.status', '!=', BookingStatus::Cancelled->value)
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.code')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();

        return [
            'customers' => $customers->map(fn ($c) => [
                'id' => $c->id,
                'name' => trim("{$c->first_name} {$c->last_name}"),
                'code' => $c->code,
                'booking_count' => (int) $c->booking_count,
                'total_spent' => (float) $c->total_spent,
            ])->all(),
        ];
    }

    protected function groupKey($date, string $groupBy): string
    {
        $carbon = Carbon::parse($date);

        return match ($groupBy) {
            'week' => $carbon->startOfWeek()->toDateString(),
            'month' => $carbon->format('Y-m'),
            default => $carbon->toDateString(),
        };
    }
}
