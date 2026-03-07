<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run this raw SQL for MySQL/Postgres to update the ENUM column
        // SQLite (testing) handles schema changes via RefreshDatabase and the updated base migration
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending', 'active', 'suspended', 'rejected') NOT NULL DEFAULT 'pending'");
        } elseif (config('database.default') === 'pgsql') {
            // Drop the old check constraint
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check");
            // Add the new check constraint
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('pending', 'active', 'suspended', 'rejected'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            // Revert back to original enum values
            // Note: This might fail if there are 'rejected' statuses in the database
            // So we update them to 'pending' first to avoid data truncation errors
            DB::table('users')->where('status', 'rejected')->update(['status' => 'pending']);
            
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending', 'active', 'suspended') NOT NULL DEFAULT 'pending'");
        } elseif (config('database.default') === 'pgsql') {
            DB::table('users')->where('status', 'rejected')->update(['status' => 'pending']);
            
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_status_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('pending', 'active', 'suspended'))");
        }
    }
};
