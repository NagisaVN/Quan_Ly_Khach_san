<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use BelongsToBranch, HasAuditColumns;

    protected $fillable = [
        'branch_id', 'product_id', 'type', 'quantity', 'stock_before',
        'stock_after', 'reference_type', 'reference_id', 'notes',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
