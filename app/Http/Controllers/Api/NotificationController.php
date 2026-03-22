<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNotificationRequest;
use App\Services\NotificationService;
use App\DTOs\NotificationDTO; 
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function store(StoreNotificationRequest $request, NotificationService $service)
    {
        try {
            $dto = NotificationDTO::fromRequest($request);
            $notification = $service->createNotification($dto);
            return response()->json([
                'message' => 'Notification queued successfully',
                'id' => $notification->id
            ], 202); 
        } catch (\Exception $e) {
            $status = ($e->getCode() >= 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            
            return response()->json([
                'error' => $e->getMessage()
            ], $status);    }
    }

/**
 * Retrieve recent notifications with filtering 
 */
public function index(Request $request): JsonResponse
{
    $notifications = Notification::query()
        ->when($request->status, fn($q) => $q->where('status', $request->status))
        ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
        ->when($request->type, fn($q) => $q->where('type', $request->type))
        ->latest()
        ->paginate(15);

    return response()->json($notifications);
}
/**
 * Summary of total notifications 
 */
public function summary(): JsonResponse
{
    $stats = Cache::remember('notification_stats_summary', 60, function () {
        return [
            'total_sent'    => Notification::where('status', 'sent')->count(),
            'total_failed'  => Notification::where('status', 'failed')->count(),
            'pending_queue' => Notification::where('status', 'pending')->count(),
            'last_updated'  => now()->toDateTimeString(),
        ];
    });

    return response()->json($stats);
}
}
