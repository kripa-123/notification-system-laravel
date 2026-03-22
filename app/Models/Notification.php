<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id', 'user_id', 'type', 'payload', 'status', 'retry_count', 'error_message'
    ];

    protected $casts = [
        'payload' => 'array',
        'status' => NotificationStatus::class,
    ];
}
