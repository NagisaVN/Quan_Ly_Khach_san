<?php

namespace App\Models;

use App\Enums\RoomStatus;
use App\Traits\BelongsToBranch;
use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use BelongsToBranch, HasAuditColumns;

    protected $fillable = [
        'branch_id',
        'floor_id',
        'room_type_id',
        'room_number',
        'status',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'status' => RoomStatus::class,
            'is_active' => 'boolean',
        ];
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenity')
            ->withTimestamps();
    }
}
