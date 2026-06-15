<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Traits\BelongsToBranch;
use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use BelongsToBranch, HasAuditColumns;

    protected $fillable = [
        'branch_id',
        'customer_id',
        'booking_code',
        'status',
        'check_in_date',
        'check_out_date',
        'expected_check_in_time',
        'expected_check_out_time',
        'actual_check_in_at',
        'actual_check_out_at',
        'source',
        'adults',
        'children',
        'total_amount',
        'deposit_amount',
        'special_requests',
        'cancellation_reason',
        'cancelled_at',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'actual_check_in_at' => 'datetime',
            'actual_check_out_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bookingRooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BookingHistory::class);
    }

    public function serviceItems(): HasMany
    {
        return $this->hasMany(BookingServiceItem::class);
    }
}
