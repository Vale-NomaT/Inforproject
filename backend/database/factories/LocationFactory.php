<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->city(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'lat' => fake()->latitude(43.6, 43.8), // Approx Toronto
            'lng' => fake()->longitude(-79.5, -79.3),
            'is_active' => true,
        ];
    }
}
