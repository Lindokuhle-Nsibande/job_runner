<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'method_name',
        'parameters',
        'status',
        'error_message',
        'retry_count',
        'priority',
        'process_id'
    ];

    protected $casts = [
        'parameters' => 'array',
    ];
}