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

        $suburbs = [
            'Ascot', 'Barham Green', 'Beacon Hill', 'Bellevue', 'Belmont', 'Bradfield', 'Burnside', 'Cement', 
            'Cowdray Park', 'Donnington', 'Douglasdale', 'Eloana', 'Emakhandeni', 'Emganwini', 'Enqameni', 
            'Entumbane', 'Famona', 'Four Winds', 'Glengarry', 'Glenville', 'Granite Park', 'Greenhill', 
            'Gwabalanda', 'Harrisvale', 'Highmount', 'Hillcrest', 'Hillside', 'Hillside South', 'Hume Park', 
            'Hyde Park', 'Ilanda', 'Iminyela', 'Intinta', 'Jacaranda', 'Kelvin', 'Kenilworth', 'Khumalo', 
            'Kilmarnock', 'Kingsdale', 'Killarney', 'Kumalo', 'Lakeside', 'Lobenvale', 'Lochview', 'Luveve', 
            'Mabuthweni', 'Magwegwe', 'Magwegwe North', 'Magwegwe West', 'Mahatshula', 'Makhandeni', 
            'Makokoba', 'Malindela', 'Manningdale', 'Marimba', 'Matsheumhlope', 'Matshobana', 'Mganwini', 
            'Montrose', 'Morningside', 'Mpopoma', 'Mzilikazi', 'New Luveve', 'New Parklands', 'Newton West', 
            'Nguboyenja', 'Nketa', 'Nkulumane', 'North End', 'North Trenance', 'Northvale', 'Old Luveve', 
            'Old Magwegwe', 'Old Nic', 'Old Pumula', 'Paddonhurst', 'Parklands', 'Parkview', 'Pelandaba', 
            'Pelandaba West', 'Pumula', 'Pumula East', 'Pumula North', 'Pumula South', 'Queens Park', 
            'Queens Park East', 'Queens Park West', 'Rangemore', 'Raylton', 'Richmond', 'Riverside', 
            'Romney Park', 'Sauerstown', 'Selborne Park', 'Sizinda', 'Southwold', 'Steeldale', 'Suburbs', 
            'Sunnyside', 'Tegela', 'The Jungle', 'Thorngrove', 'Trenance', 'Tshabalala', 'Upper Rangemore', 
            'Waterford', 'West Somerton', 'Westgate', 'Westondale', 'Whitestone', 'Windsor Park', 'Woodlands', 
            'Worringham'
        ];

        $locations = [];
        // Base coordinates for Bulawayo
        $baseLat = -20.155300;
        $baseLng = 28.581300;

        foreach ($suburbs as $index => $suburb) {
            $locations[] = [
                'name' => $suburb,
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                // Add small variation to separate points on map slightly, 
                // though real coords would be better, this prevents stacking exactly on one spot
                'lat' => $baseLat + (mt_rand(-500, 500) / 10000), 
                'lng' => $baseLng + (mt_rand(-500, 500) / 10000),
                'is_active' => true,
            ];
        }

        // Add specific key locations with more accurate coords if known (overwriting generic ones if needed)
        $specifics = [
            ['name' => 'Bulawayo City Hall', 'lat' => -20.155300, 'lng' => 28.581300],
            ['name' => 'Bradfield Shopping Centre', 'lat' => -20.173000, 'lng' => 28.591000],
            ['name' => 'Hillside Dams Conservancy', 'lat' => -20.198000, 'lng' => 28.615000],
        ];

        foreach ($specifics as $spec) {
            $locations[] = [
                'name' => $spec['name'],
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => $spec['lat'],
                'lng' => $spec['lng'],
                'is_active' => true,
            ];
        }

        Location::insert($locations);
    }
}
