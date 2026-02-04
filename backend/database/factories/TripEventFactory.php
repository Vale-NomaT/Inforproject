<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Trip;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripEvent>
 */
class TripEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'event_type' => fake()->randomElement(['started', 'picked_up', 'dropped_off', 'arrived']),
            'location_lat' => fake()->latitude(),
            'location_lng' => fake()->longitude(),
            'occurred_at' => now(),
        ];
    }
}
