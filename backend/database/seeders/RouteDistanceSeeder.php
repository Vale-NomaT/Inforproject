<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RouteDistance;
use App\Models\Location;
use App\Models\School;

class RouteDistanceSeeder extends Seeder
{
    public function run(): void
    {
        $cityHall = Location::where('name', 'Bulawayo City Hall')->first();
        $bradfield = Location::where('name', 'Bradfield Shopping Centre')->first();
        $hillsideDams = Location::where('name', 'Hillside Dams Conservancy')->first();

        $whitestone = School::where('name', 'Whitestone School')->first();
        $hillsideSchool = School::where('name', 'Hillside Primary School')->first();
        $petra = School::where('name', 'Petra Primary School')->first();

        if ($cityHall && $whitestone) {
            RouteDistance::create([
                'location_id' => $cityHall->id,
                'school_id' => $whitestone->id,
                'one_way_distance_km' => 12.0,
                'last_calculated' => now(),
            ]);
        }

        if ($bradfield && $hillsideSchool) {
            RouteDistance::create([
                'location_id' => $bradfield->id,
                'school_id' => $hillsideSchool->id,
                'one_way_distance_km' => 8.5,
                'last_calculated' => now(),
            ]);
        }

        if ($hillsideDams && $petra) {
            RouteDistance::create([
                'location_id' => $hillsideDams->id,
                'school_id' => $petra->id,
                'one_way_distance_km' => 15.2,
                'last_calculated' => now(),
            ]);
        }
    }
}
