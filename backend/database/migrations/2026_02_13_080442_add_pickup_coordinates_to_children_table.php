<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->decimal('pickup_lat', 10, 8)->nullable()->after('pickup_location_id');
            $table->decimal('pickup_lng', 11, 8)->nullable()->after('pickup_lat');
            $table->string('pickup_address')->nullable()->after('pickup_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['pickup_lat', 'pickup_lng', 'pickup_address']);
        });
    }
};
