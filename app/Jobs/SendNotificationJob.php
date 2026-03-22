<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Enums\NotificationStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    // public $backoff = [60, 300, 900, 1800]; 
        public $backoff = [5, 10, 15]; 

    /**
     * Create a new job instance.
     */
    public function __construct( protected Notification $notification)
    {
        //
    }

    /**
     * Execute the job.
     */
       public function handle(): void
    {
        $this->notification->update(['status' => NotificationStatus::PROCESSING]);

        try {
                if (empty($this->notification->payload)) {
                    throw new \Exception("Invalid Payload");
                }
            
             if (isset($this->notification->payload['force_fail']) && $this->notification->payload['force_fail'] === true) 
            {
                throw new \Exception("Simulated Delivery Failure");
            }

            Log::info("Processing Notification ID: {$this->notification->id}", [
                'type' => $this->notification->type,
                'user_id' => $this->notification->user_id,
                'content' => $this->notification->payload['body'] ?? 'No content'
            ]);
            
            $this->notification->update([
                'status' => NotificationStatus::SENT,
                'error_message' => null
            ]);

        } catch (\Exception $e) {
            $this->notification->update([
                'status' => NotificationStatus::FAILED,
                'error_message' => $e->getMessage(),
                'retry_count' => $this->attempts()
            ]);

                    \Log::error($e->getMessage());

            throw $e; 
        }
    }

    public function failed(\Throwable $exception): void
{
    Log::error("Notification {$this->notification->id} failed after all retries.", [
        'error' => $exception->getMessage()
    ]);

    $this->notification->update([
        'status' => NotificationStatus::FAILED,
        'error_message' => 'Max retries exhausted: ' . $exception->getMessage()
    ]);
}
}
