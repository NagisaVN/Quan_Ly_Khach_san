<?php

namespace App\Models;

use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingServiceItem extends Model
{
    use HasAuditColumns;

    protected $table = 'booking_services';

    protected $fillable = [
        'booking_id',
        'service_id',
        'quantity',
        'unit_price',
        'total_amount',
        'service_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'service_date' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(HotelService::class, 'service_id');
    }
}
