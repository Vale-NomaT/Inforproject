<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('pickup_location_id');
            $table->text('medical_notes')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('parents')
                ->onDelete('cascade');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');

            $table->foreign('pickup_location_id')
                ->references('id')
                ->on('locations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
