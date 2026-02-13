<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class EnsureDatabaseTables extends Command
{
    protected $signature = 'db:ensure-tables';
    protected $description = 'Checks for missing tables and re-runs migrations if necessary';

    public function handle()
    {
        $this->info('Checking database tables...');

        // Map critical tables to their migration patterns
        $tableChecks = [
            'users' => 'create_users_table',
            'parents' => 'create_parents_and_drivers_tables',
            'drivers' => 'create_parents_and_drivers_tables',
            'locations' => 'create_locations_and_schools_tables',
            'schools' => 'create_locations_and_schools_tables',
            'children' => 'create_children_table',
            'driver_locations' => 'create_driver_location_and_driver_school_tables',
            'driver_schools' => 'create_driver_location_and_driver_school_tables',
            'route_distances' => 'create_route_distances_table',
            'booking_requests' => 'create_booking_requests_table',
            'trips' => 'create_trips_table',
            'trip_events' => 'create_trip_events_table',
            'ratings' => 'create_ratings_table',
            'driver_performance_scores' => 'create_driver_performance_scores_table',
        ];

        $missingTables = [];
        $migrationsToReset = [];

        foreach ($tableChecks as $table => $migrationPattern) {
            if (!Schema::hasTable($table)) {
                $this->error("Missing table: {$table}");
                $missingTables[] = $table;
                
                // Find if the migration was already marked as run
                $migrationEntry = DB::table('migrations')
                    ->where('migration', 'like', "%{$migrationPattern}%")
                    ->first();

                if ($migrationEntry) {
                    $this->warn("Migration for {$table} marked as run, but table is missing. Resetting migration record...");
                    DB::table('migrations')
                        ->where('migration', 'like', "%{$migrationPattern}%")
                        ->delete();
                    $migrationsToReset[] = $migrationPattern;
                }
            } else {
                $this->line("✓ Table exists: {$table}");
            }
        }

        if (empty($missingTables)) {
            $this->info('All critical tables exist.');
            return;
        }

        $this->info('Attempting to recreate missing tables...');
        
        // Run migrations
        // We use --force because this might run in production
        $exitCode = Artisan::call('migrate', ['--force' => true]);
        
        $this->info(Artisan::output());

        if ($exitCode === 0) {
            $this->info('Database repair completed successfully.');
        } else {
            $this->error('Migration failed.');
        }
    }
}
