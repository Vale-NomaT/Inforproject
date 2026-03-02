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
        // Update the enum check constraint for PostgreSQL
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Drop the old constraint
            DB::statement("ALTER TABLE trip_events DROP CONSTRAINT IF EXISTS trip_events_type_check");
            
            // Add the new constraint with 'arrived_dropoff' included
            DB::statement("ALTER TABLE trip_events ADD CONSTRAINT trip_events_type_check CHECK (type::text = ANY (ARRAY['started'::character varying, 'arrived'::character varying, 'picked_up'::character varying, 'arrived_dropoff'::character varying, 'dropped_off'::character varying]::text[]))");
        } else {
            // For MySQL/SQLite, we can use the Schema builder to modify the column if supported
            // or just let it be if it's not strictly enforced by a check constraint in the same way
            // But to be safe for other environments:
            try {
                Schema::table('trip_events', function (Blueprint $table) {
                    $table->string('type')->change(); // Temporarily change to string to remove enum constraint
                });
                Schema::table('trip_events', function (Blueprint $table) {
                    $table->enum('type', ['started', 'arrived', 'picked_up', 'arrived_dropoff', 'dropped_off'])->change();
                });
            } catch (\Exception $e) {
                // Ignore if modification not supported or failed, as the critical fix is for Postgres
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE trip_events DROP CONSTRAINT IF EXISTS trip_events_type_check");
            DB::statement("ALTER TABLE trip_events ADD CONSTRAINT trip_events_type_check CHECK (type::text = ANY (ARRAY['started'::character varying, 'arrived'::character varying, 'picked_up'::character varying, 'dropped_off'::character varying]::text[]))");
        }
    }
};
