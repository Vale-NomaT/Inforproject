<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ParentProfile>
 */
class ParentProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => User::factory(),
            'relationship_to_child' => fake()->randomElement(['Mother', 'Father', 'Guardian']),
            'secondary_phone' => fake()->phoneNumber(),
            'address_street' => fake()->streetAddress(),
            'address_city' => fake()->city(),
            'address_country' => fake()->country(),
        ];
    }
}
