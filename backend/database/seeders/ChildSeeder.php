<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Child;
use App\Models\User;
use App\Models\School;
use App\Models\Location;

class ChildSeeder extends Seeder
{
    public function run(): void
    {
        $sarah = User::where('email', 'sarah@example.com')->first();
        if (!$sarah) return;

        $whitestone = School::where('name', 'Whitestone School')->first();
        $cityHall = Location::where('name', 'Bulawayo City Hall')->first();

        if ($whitestone && $cityHall) {
            Child::create([
                'parent_id' => $sarah->id,
                'first_name' => 'Emma',
                'last_name' => 'L.',
                'date_of_birth' => now()->subYears(8),
                'school_id' => $whitestone->id,
                'pickup_location_id' => $cityHall->id,
                'medical_notes' => 'Mild peanut allergy',
            ]);
        }

        $hillside = School::where('name', 'Hillside Primary School')->first();
        $bradfield = Location::where('name', 'Bradfield Shopping Centre')->first();

        if ($hillside && $bradfield) {
            Child::create([
                'parent_id' => $sarah->id,
                'first_name' => 'Liam',
                'last_name' => 'J.',
                'date_of_birth' => now()->subYears(6),
                'school_id' => $hillside->id,
                'pickup_location_id' => $bradfield->id,
                'medical_notes' => 'None',
            ]);
        }
    }
}
