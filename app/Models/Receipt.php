<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Receipt extends Model {
    protected $fillable = ['payment_id','receipt_number','amount','path','issued_at'];
    protected function casts(): array { return ['amount'=>'decimal:2','issued_at'=>'datetime']; }
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
}
