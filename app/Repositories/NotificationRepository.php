<?php
namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository
{
    public function getRecent(array $filters)
    {
        return Notification::query()
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15);
    }

    public function getStats()
    {
        return [
            'sent'    => Notification::where('status', 'sent')->count(),
            'failed'  => Notification::where('status', 'failed')->count(),
            'pending' => Notification::where('status', 'pending')->count(),
        ];
    }
}