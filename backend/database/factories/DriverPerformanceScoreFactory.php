<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DriverPerformanceScore>
 */
class DriverPerformanceScoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'driver_id' => User::factory()->state(['user_type' => 'driver']),
            'total_trips' => fake()->numberBetween(10, 100),
            'average_rating' => fake()->randomFloat(1, 4, 5),
            'on_time_rate' => fake()->randomFloat(2, 0.9, 1.0),
            'safety_score' => fake()->randomFloat(2, 0.9, 1.0),
            'overall_score' => fake()->randomFloat(2, 90, 100),
        ];
    }
}
