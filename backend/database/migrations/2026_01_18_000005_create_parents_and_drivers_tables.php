<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
            $table->string('relationship_to_child', 50);
            $table->string('secondary_phone', 20)->nullable();
            $table->text('address_street')->nullable();
            $table->string('address_city', 100)->nullable();
            $table->string('address_country', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
            $table->date('date_of_birth')->nullable();
            $table->string('gov_id_number', 100)->nullable();
            $table->string('license_number', 100);
            $table->string('vehicle_make', 100)->nullable();
            $table->string('vehicle_model', 50)->nullable();
            $table->smallInteger('vehicle_year')->nullable();
            $table->string('vehicle_color', 30)->nullable();
            $table->string('license_plate', 20)->nullable();
            $table->tinyInteger('max_child_capacity');
            $table->string('vehicle_type', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('parents');
    }
};
