<?php

namespace App\Models;

use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasAuditColumns;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'max_occupancy',
        'max_adults',
        'max_children',
        'base_price',
        'area_sqm',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'area_sqm' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
