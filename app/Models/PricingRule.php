<?php
namespace App\Models;
use App\Traits\BelongsToBranch; use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PricingRule extends Model {
    use BelongsToBranch, HasAuditColumns;
    protected $fillable = ["branch_id","room_type_id","name","type","conditions","adjustment_type","value","priority","valid_from","valid_to","is_active"];
    protected function casts(): array { return ["conditions"=>"array","value"=>"decimal:2","valid_from"=>"date","valid_to"=>"date","is_active"=>"boolean"]; }
    public function roomType(): BelongsTo { return $this->belongsTo(RoomType::class); }
}