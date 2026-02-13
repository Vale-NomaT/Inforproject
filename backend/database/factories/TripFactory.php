<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Child;
use App\Models\Trip;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    public function definition(): array
    {
        return [
            'driver_id' => User::factory()->state(['user_type' => 'driver']),
            'child_id' => Child::factory(),
            'scheduled_date' => fake()->date(),
            'type' => fake()->randomElement(['morning', 'afternoon']),
            'status' => Trip::STATUS_SCHEDULED,
            'distance_km' => fake()->randomFloat(2, 2, 20),
            'pricing_tier' => fake()->randomElement(['tier1', 'tier2']),
        ];
    }
}
