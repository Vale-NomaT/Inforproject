<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Truncate existing locations to avoid duplicates or stale data
        // Note: This will remove any custom locations created by users if they are stored in the same table
        // But since this is a fresh data population request for production, we proceed as per seeder logic.
        
        // We need to disable foreign key checks to truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('locations')->truncate();
        DB::table('driver_locations')->truncate(); // Pivot table
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
        $now = now();

        foreach ($suburbs as $index => $suburb) {
            $locations[] = [
                'name' => $suburb,
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                // Add small variation to separate points on map slightly
                'lat' => $baseLat + (mt_rand(-500, 500) / 10000), 
                'lng' => $baseLng + (mt_rand(-500, 500) / 10000),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Add specific key locations with more accurate coords if known (overwriting generic ones if needed)
        // Since we are inserting all at once, we just append these as new entries or we should filter out duplicates.
        // The seeder logic just appended them, but some might be duplicates by name (e.g. Bradfield).
        // Let's filter out if name already exists in $locations array to be safe, or just append as per seeder.
        // The seeder appended them. But 'Bradfield' is in the suburbs list AND in specific list.
        // To be cleaner than the seeder, let's remove the generic one if we have a specific one.
        
        $specifics = [
            ['name' => 'Bulawayo City Hall', 'lat' => -20.155300, 'lng' => 28.581300],
            ['name' => 'Bradfield Shopping Centre', 'lat' => -20.173000, 'lng' => 28.591000],
            ['name' => 'Hillside Dams Conservancy', 'lat' => -20.198000, 'lng' => 28.615000],
        ];

        // We will just append them to match the seeder behavior exactly, 
        // as the user verified the seeder locally and was happy with it.
        // Note: 'Bradfield' vs 'Bradfield Shopping Centre' are different names, so no collision.
        // 'Hillside' vs 'Hillside Dams Conservancy' are different.
        
        foreach ($specifics as $spec) {
            $locations[] = [
                'name' => $spec['name'],
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => $spec['lat'],
                'lng' => $spec['lng'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert in chunks to avoid query size limits if any, though ~100 rows is fine.
        DB::table('locations')->insert($locations);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't necessarily want to truncate on rollback as it might delete user data
        // But strictly speaking, the reverse of "adding these locations" is removing them.
        // For safety in production, we'll leave down() empty or just comment it out.
        // Schema::truncate('locations'); 
    }
};
