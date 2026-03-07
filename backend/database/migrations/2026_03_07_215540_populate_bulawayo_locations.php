<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $suburbs = [
            'Ascot', 'Barham Green', 'Beacon Hill', 'Bellevue', 'Belmont', 'Bradfield', 'Burnside', 'Cement', 
            'Cowdray Park', 'Donnington', 'Donnington West', 'Douglasdale', 'Eloana', 'Emakhandeni', 'Emganwini', 'Enqameni', 
            'Enqotsheni', 'Entumbane', 'Fagadola', 'Famona', 'Fortunes Gate', 'Four Winds', 'Glencoe', 'Glengarry', 'Glenville', 
            'Granite Park', 'Greenhill', 'Gwabalanda', 'Harrisvale', 'Helenvale', 'Highmount', 'Hillcrest', 'Hillside', 
            'Hillside South', 'Hume Park', 'Hyde Park', 'Ilanda', 'Iminyela', 'Intinta', 'Jacaranda', 'Kelvin', 
            'Kenilworth', 'Khumalo', 'Kilmarnock', 'Kingsdale', 'Killarney', 'Kumalo', 'Lakeside', 'Lobenvale', 
            'Lochview', 'Luveve', 'Mabuthweni', 'Magwegwe', 'Magwegwe North', 'Magwegwe West', 'Mahatshula', 
            'Makhandeni', 'Makokoba', 'Malindela', 'Manningdale', 'Marimba', 'Matsheumhlope', 'Matshobana', 
            'Mganwini', 'Montrose', 'Morningside', 'Mpopoma', 'Mzilikazi', 'New Luveve', 'New Parklands', 
            'Newton West', 'Nguboyenja', 'Nketa', 'Nkulumane', 'North End', 'North Trenance', 'Northvale', 
            'Old Luveve', 'Old Magwegwe', 'Old Nic', 'Old Pumula', 'Paddonhurst', 'Parklands', 'Parkview', 
            'Pelandaba', 'Pelandaba West', 'Pumula', 'Pumula East', 'Pumula North', 'Pumula South', 'Queens Park', 
            'Queens Park East', 'Queens Park West', 'Rangemore', 'Raylton', 'Richmond', 'Riverside', 'Romney Park', 
            'Sauerstown', 'Selborne Park', 'Sizinda', 'Southwold', 'Steeldale', 'Suburbs', 'Sunnyside', 'Tegela', 
            'The Jungle', 'Thorngrove', 'Trenance', 'Tshabalala', 'Upper Rangemore', 'Waterford', 'West Somerton', 
            'Westgate', 'Westondale', 'Whitestone', 'Windsor Park', 'Woodlands', 'Worringham'
        ];

        // Base coordinates for Bulawayo
        $baseLat = -20.155300;
        $baseLng = 28.581300;
        $now = now();

        foreach ($suburbs as $suburb) {
            $exists = DB::table('locations')
                ->where('name', $suburb)
                ->where('city', 'Bulawayo')
                ->exists();

            if (!$exists) {
                DB::table('locations')->insert([
                    'name' => $suburb,
                    'city' => 'Bulawayo',
                    'country' => 'Zimbabwe',
                    // Add small variation to separate points on map slightly
                    'lat' => $baseLat + (mt_rand(-500, 500) / 10000), 
                    'lng' => $baseLng + (mt_rand(-500, 500) / 10000),
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Add specific key locations with more accurate coords if known (check existence first)
        $specifics = [
            ['name' => 'Bulawayo City Hall', 'lat' => -20.155300, 'lng' => 28.581300],
            ['name' => 'Bradfield Shopping Centre', 'lat' => -20.173000, 'lng' => 28.591000],
            ['name' => 'Hillside Dams Conservancy', 'lat' => -20.198000, 'lng' => 28.615000],
        ];

        foreach ($specifics as $spec) {
            $exists = DB::table('locations')
                ->where('name', $spec['name'])
                ->where('city', 'Bulawayo')
                ->exists();

            if (!$exists) {
                DB::table('locations')->insert([
                    'name' => $spec['name'],
                    'city' => 'Bulawayo',
                    'country' => 'Zimbabwe',
                    'lat' => $spec['lat'],
                    'lng' => $spec['lng'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ideally we would delete only what we added, but since we're populating "all Bulawayo suburbs",
        // cleaning up all Bulawayo locations might be appropriate if we want to truly reverse.
        // However, user data might be linked to these, so standard practice is often to do nothing or be very careful.
        // For this specific task, we'll leave it empty to avoid deleting data that might be in use.
    }
};
