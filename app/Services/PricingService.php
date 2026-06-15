<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Holiday;
use App\Models\PricingRule;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SeasonalRate;
use Carbon\Carbon;

class PricingService
{
    public function calculateNightlyRate(int $roomTypeId, int $branchId, Carbon $date, ?int $customerId = null): float
    {
        $roomType = RoomType::findOrFail($roomTypeId);
        $rate = (float) $roomType->base_price;

        $seasonal = SeasonalRate::query()
            ->where('branch_id', $branchId)
            ->where('room_type_id', $roomTypeId)
            ->where('is_active', true)
            ->where('start_date', '<=', $date->toDateString())
            ->where('end_date', '>=', $date->toDateString())
            ->first();

        if ($seasonal) {
            $rate = (float) ($seasonal->rate ?? $rate);
            if ($seasonal->adjustment_percent) {
                $rate += $rate * ((float) $seasonal->adjustment_percent / 100);
            }
        }

        if ($date->isWeekend()) {
            $rate *= 1.1;
        }

        if ($this->isHoliday($branchId, $date)) {
            $rate *= 1.15;
        }

        $occupancyRate = $this->getOccupancyRate($branchId, $date);
        if ($occupancyRate >= 80) {
            $rate *= 1.2;
        } elseif ($occupancyRate >= 60) {
            $rate *= 1.1;
        }

        if ($customerId) {
            $customer = Customer::with('loyaltyTier')->find($customerId);
            if ($customer?->loyaltyTier?->discount_percent) {
                $rate -= $rate * ((float) $customer->loyaltyTier->discount_percent / 100);
            }
        }

        $rules = PricingRule::query()
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('room_type_id')->orWhere('room_type_id', $roomTypeId))
            ->where(fn ($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', $date->toDateString()))
            ->where(fn ($q) => $q->whereNull('valid_to')->orWhere('valid_to', '>=', $date->toDateString()))
            ->orderByDesc('priority')
            ->get();

        foreach ($rules as $rule) {
            if ($rule->adjustment_type === 'percent') {
                $rate += $rate * ((float) $rule->value / 100);
            } else {
                $rate += (float) $rule->value;
            }
        }

        return max(0, round($rate, 2));
    }

    public function calculateBookingTotal(array $roomLines): float
    {
        return array_sum(array_column($roomLines, 'total'));
    }

    protected function isHoliday(int $branchId, Carbon $date): bool
    {
        return Holiday::query()
            ->where('is_active', true)
            ->where('date', $date->toDateString())
            ->exists();
    }

    protected function getOccupancyRate(int $branchId, Carbon $date): float
    {
        $totalRooms = Room::query()
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->count();

        if ($totalRooms === 0) {
            return 0;
        }

        $occupied = Room::query()
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->whereIn('status', ['occupied', 'reserved'])
            ->count();

        return round(($occupied / $totalRooms) * 100, 2);
    }
}
