<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonalRate extends Model
{
    use BelongsToBranch, HasAuditColumns;

    protected $fillable = ['branch_id','room_type_id','name','start_date','end_date','rate','adjustment_percent','is_active'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'rate' => 'decimal:2',
            'adjustment_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}
