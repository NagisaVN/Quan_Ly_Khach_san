<?php

namespace App\Models;

use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    use HasAuditColumns;

    protected $fillable = ['key', 'value', 'group', 'type', 'description'];
}
