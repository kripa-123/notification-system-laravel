<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Notification;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\Queue; 

class NotificationQueueTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_it_updates_status_on_successful_processing()
{
    Queue::fake();
    $notification = Notification::factory()->create(['status' => 'pending']);

    // Manually trigger the job
    (new SendNotificationJob($notification))->handle(); 

    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
        'status' => 'sent'
    ]);
}

// public function test_it_logs_error_and_retries_on_failure()
// {
//     // Simulate a failing notification logic
//     $notification = Notification::factory()->create(['status' => 'pending']);
    
//     // We expect the job to fail and throw an exception to trigger Laravel's retry
//     $this->expectException(\Exception::class);

//     $job = new SendNotificationJob($notification);
//     // You could mock a failing service here
//     $job->handle();
// }

public function test_it_logs_error_and_retries_on_failure()
{
    // 1. Create a notification designed to fail
    $notification = Notification::factory()->create([
        'status' => 'pending',
        'payload' => ['force_fail' => true, 'title' => 'Fail Test', 'body' => '...']
    ]);

    // 2. Expect the Exception
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("Simulated Delivery Failure");

    // 3. Trigger the handle method manually
    $job = new SendNotificationJob($notification);
    $job->handle(); // This will now throw the exception
}
}
