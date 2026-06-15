<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Traits\BelongsToBranch;
use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use BelongsToBranch, HasAuditColumns;

    protected $fillable = [
        'invoice_id',
        'branch_id',
        'payment_number',
        'amount',
        'payment_method',
        'status',
        'reference',
        'transaction_id',
        'gateway_response',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
