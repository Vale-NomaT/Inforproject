<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Location;
use App\Models\School;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RouteDistance>
 */
class RouteDistanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'school_id' => School::factory(),
            'one_way_distance_km' => fake()->randomFloat(2, 5, 20),
            'last_calculated' => now(),
        ];
    }
}
