<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User; 
use Illuminate\Support\Facades\Cache;

class NotificationApiTest extends TestCase
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
 public function test_user_cannot_exceed_rate_limit()
{
    $user = User::factory()->create();

    for ($i = 0; $i < 10; $i++) {
        $this->actingAs($user)->postJson('/api/notifications', [
            'user_id' => $user->id,
            'tenant_id' => 1, 
            'type' => 'email',
            'payload' => ['title' => 'Test', 'body' => 'Hello']
        ])->assertStatus(202);
    }

    $this->actingAs($user)->postJson('/api/notifications', [
        'user_id' => $user->id,
        'tenant_id' => 1, 
        'type' => 'email',
        'payload' => ['title' => 'Test', 'body' => 'Hello']
    ])->assertStatus(429);
}
}
