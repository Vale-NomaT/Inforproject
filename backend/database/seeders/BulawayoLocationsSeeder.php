<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Location;

class BulawayoLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Location::truncate();
        DB::table('driver_locations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $locations = [
            [
                'name' => 'Bulawayo City Hall',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.155300,
                'lng' => 28.581300,
                'is_active' => true,
            ],
            [
                'name' => 'Bradfield Shopping Centre',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.173000,
                'lng' => 28.591000,
                'is_active' => true,
            ],
            [
                'name' => 'Hillside Dams Conservancy',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.198000,
                'lng' => 28.615000,
                'is_active' => true,
            ],
            [
                'name' => 'Ascot Shopping Centre',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.149000,
                'lng' => 28.604000,
                'is_active' => true,
            ],
            [
                'name' => 'North End',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.130000,
                'lng' => 28.590000,
                'is_active' => true,
            ],
            [
                'name' => 'Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.165000,
                'lng' => 28.595000,
                'is_active' => true,
            ],
            [
                'name' => 'Kumalo',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.145000,
                'lng' => 28.600000,
                'is_active' => true,
            ],
            [
                'name' => 'Famona',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.170000,
                'lng' => 28.580000,
                'is_active' => true,
            ],
            [
                'name' => 'Paddonhurst',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.135000,
                'lng' => 28.570000,
                'is_active' => true,
            ],
            [
                'name' => 'Sauerstown',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.115000,
                'lng' => 28.575000,
                'is_active' => true,
            ],
            [
                'name' => 'Belmont Industrial',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.175000,
                'lng' => 28.565000,
                'is_active' => true,
            ],
            [
                'name' => 'Donnington',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.185000,
                'lng' => 28.570000,
                'is_active' => true,
            ],
            [
                'name' => 'Morningside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.180000,
                'lng' => 28.600000,
                'is_active' => true,
            ],
            [
                'name' => 'Burnside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.210000,
                'lng' => 28.617000,
                'is_active' => true,
            ],
            [
                'name' => 'Woodlands',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.190000,
                'lng' => 28.625000,
                'is_active' => true,
            ],
            [
                'name' => 'Ilanda',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.185000,
                'lng' => 28.615000,
                'is_active' => true,
            ],
            [
                'name' => 'Malindela',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.180000,
                'lng' => 28.590000,
                'is_active' => true,
            ],
            [
                'name' => 'Barham Green',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.175000,
                'lng' => 28.575000,
                'is_active' => true,
            ],
            [
                'name' => 'Montrose',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.195000,
                'lng' => 28.585000,
                'is_active' => true,
            ],
            [
                'name' => 'Southwold',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.190000,
                'lng' => 28.575000,
                'is_active' => true,
            ],
        ];

        foreach ($locations as $loc) {
            Location::create($loc);
        }
    }
}
