<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is a "Data Migration" to run the seeder on the live environment
        // because we don't have Shell access on the free Render tier.
        
        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\BulawayoSchoolsSeeder',
            '--force' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No strict reverse action since seeding is destructive/additive
    }
};
