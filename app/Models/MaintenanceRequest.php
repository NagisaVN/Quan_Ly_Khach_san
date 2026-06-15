<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    use BelongsToBranch, HasAuditColumns;

    protected $fillable = [
        'branch_id', 'room_id', 'title', 'description', 'priority', 'status',
        'reported_at', 'started_at', 'completed_at', 'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
