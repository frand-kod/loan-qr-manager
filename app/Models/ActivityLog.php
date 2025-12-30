<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    // standalone tablesystem

    protected $fillable = [
        'user_id',
        'activity',
        'description',
        'ip_address',
        'user_agent',
    ];
}
