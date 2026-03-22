<?php

namespace Database\Factories;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Notification::class;
    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'user_id' => 1,
            'type' => 'email',
            'payload' => [
                'title' => 'Test Notification',
                'body' => 'This is a test message'
            ],
            'status' => 'pending',
        ];
    }
}
