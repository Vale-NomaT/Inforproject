<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('location_id');

            $table->primary(['driver_id', 'location_id']);

            $table->foreign('driver_id')
                ->references('id')
                ->on('drivers')
                ->onDelete('cascade');

            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
                ->onDelete('cascade');
        });

        Schema::create('driver_schools', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('school_id');

            $table->primary(['driver_id', 'school_id']);

            $table->foreign('driver_id')
                ->references('id')
                ->on('drivers')
                ->onDelete('cascade');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_schools');
        Schema::dropIfExists('driver_locations');
    }
};
