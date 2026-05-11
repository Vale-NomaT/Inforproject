<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't enforce enums — the 'cancelled' value just works.
        // For MySQL/Postgres we alter the column.
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE trips MODIFY COLUMN status ENUM('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE trips DROP CONSTRAINT IF EXISTS trips_status_check");
            DB::statement("ALTER TABLE trips ADD CONSTRAINT trips_status_check CHECK (status IN ('scheduled','in_progress','completed','cancelled'))");
        }
        // SQLite: no action needed

        Schema::create('trip_absences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->unsignedBigInteger('parent_id');
            $table->date('start_date');
            $table->date('end_date');                          // same as start_date for single-day
            $table->enum('run_type', ['morning', 'afternoon', 'both'])->default('both');
            $table->string('reason')->nullable();
            $table->timestamp('driver_notified_at')->nullable();
            $table->timestamps();

            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_absences');
    }
};
