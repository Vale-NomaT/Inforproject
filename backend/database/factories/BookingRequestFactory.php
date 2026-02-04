<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Child;
use App\Models\BookingRequest;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingRequest>
 */
class BookingRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_id' => User::factory()->state(['user_type' => 'parent']),
            'driver_id' => User::factory()->state(['user_type' => 'driver']),
            'child_id' => Child::factory(),
            'status' => BookingRequest::STATUS_PENDING,
            'pricing_tier' => fake()->randomElement(['tier1', 'tier2']),
            'created_at' => now(),
            'responded_at' => null,
        ];
    }
}
