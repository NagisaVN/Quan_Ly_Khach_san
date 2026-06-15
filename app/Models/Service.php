<?php
namespace App\Models;
use App\Traits\BelongsToBranch; use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Service extends Model {
    use BelongsToBranch, HasAuditColumns;
    protected $fillable = ["branch_id","service_category_id","name","code","description","unit_price","unit","is_active"];
    protected function casts(): array { return ["unit_price"=>"decimal:2","is_active"=>"boolean"]; }
    public function category(): BelongsTo { return $this->belongsTo(ServiceCategory::class, "service_category_id"); }
}