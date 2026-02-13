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
        // Run seeders to populate locations and schools on deployment
        Artisan::call('db:seed', ['--class' => 'BulawayoLocationsSeeder']);
        Artisan::call('db:seed', ['--class' => 'BulawayoSchoolsSeeder']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed as data population is not strictly schema structure
    }
};
