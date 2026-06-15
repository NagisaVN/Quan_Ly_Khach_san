<?php

namespace App\Models;

use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasAuditColumns;

    protected $fillable = [
        'filename', 'disk', 'path', 'size', 'type', 'status', 'notes',
    ];
}
