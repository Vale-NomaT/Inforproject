<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\School;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Pickup Zones
        $downtown = Location::create([
            'name' => 'Downtown',
            'city' => 'Toronto',
            'country' => 'Canada',
            'lat' => 43.6532,
            'lng' => -79.3832,
            'is_active' => true,
        ]);

        $westside = Location::create([
            'name' => 'Westside',
            'city' => 'Toronto',
            'country' => 'Canada',
            'lat' => 43.6416,
            'lng' => -79.4312,
            'is_active' => true,
        ]);

        $northHills = Location::create([
            'name' => 'North Hills',
            'city' => 'Toronto',
            'country' => 'Canada',
            'lat' => 43.7416,
            'lng' => -79.4000,
            'is_active' => true,
        ]);

        // Schools
        $greenwood = School::create([
            'name' => 'Greenwood Primary',
            'address' => '123 Greenwood Ave',
            'city' => 'Toronto',
            'country' => 'Canada',
            'lat' => 43.6600,
            'lng' => -79.3500,
            'is_active' => true,
        ]);

        $hillside = School::create([
            'name' => 'Hillside Academy',
            'address' => '456 Hillside Rd',
            'city' => 'Toronto',
            'country' => 'Canada',
            'lat' => 43.6800,
            'lng' => -79.4500,
            'is_active' => true,
        ]);

        $oakridge = School::create([
            'name' => 'Oakridge School',
            'address' => '789 Oakridge Blvd',
            'city' => 'Toronto',
            'country' => 'Canada',
            'lat' => 43.7000,
            'lng' => -79.3000,
            'is_active' => true,
        ]);
    }
}
