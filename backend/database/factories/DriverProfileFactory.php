<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DriverProfile>
 */
class DriverProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => User::factory(), // Should be overridden or used with create()
            'date_of_birth' => fake()->date('Y-m-d', '-25 years'),
            'gov_id_number' => fake()->bothify('??######'),
            'license_number' => fake()->bothify('??-####-####'),
            'vehicle_make' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'Nissan']),
            'vehicle_model' => fake()->word(),
            'vehicle_year' => fake()->year(),
            'vehicle_color' => fake()->safeColorName(),
            'license_plate' => fake()->bothify('???-####'),
            'max_child_capacity' => fake()->numberBetween(3, 7),
            'vehicle_type' => 'sedan',
        ];
    }
}
