<?php

namespace App\Services;
use App\DTOs\NotificationDTO;
use App\Models\Notification;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    public function createNotification(NotificationDTO $data)
    {
        $key = "user_notify_limit:{$data->user_id}";
        
        if (RateLimiter::tooManyAttempts($key, 10)) {
            throw new \Exception("Rate limit exceeded. Try again later.", 429);
        }
        $notification = Notification::create([
            'tenant_id' => $data->tenant_id,
            'user_id' => $data->user_id,
            'type' => $data->type,
            'payload' => $data->payload,
            'status' => 'pending'
        ]);

        RateLimiter::hit($key, 3600); 

        SendNotificationJob::dispatch($notification);

        return $notification;
    }
}