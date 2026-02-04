<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Location;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Child>
 */
class ChildFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_id' => ParentProfile::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->dateTimeBetween('-12 years', '-5 years'),
            'school_id' => School::factory(),
            'pickup_location_id' => Location::factory(),
            'medical_notes' => fake()->optional()->sentence(),
        ];
    }
}
