<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Luggage extends Model
{
    use BelongsToBranch, HasAuditColumns;

    protected $table = 'luggage';

    protected $fillable = [
        'branch_id', 'customer_id', 'booking_id', 'tag_code', 'description',
        'quantity', 'storage_location', 'status', 'stored_at', 'retrieved_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'stored_at' => 'datetime',
            'retrieved_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
