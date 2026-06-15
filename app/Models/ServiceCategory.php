<?php
namespace App\Models;
use App\Traits\BelongsToBranch; use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany;
class ServiceCategory extends Model {
    use BelongsToBranch, HasAuditColumns;
    protected $fillable = ["branch_id","name","code","description","sort_order","is_active"];
    protected function casts(): array { return ["is_active"=>"boolean"]; }
    public function services(): HasMany { return $this->hasMany(Service::class); }
}