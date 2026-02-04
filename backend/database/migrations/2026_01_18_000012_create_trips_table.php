<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('child_id');
            $table->date('scheduled_date');
            $table->enum('status', ['scheduled', 'in_progress', 'completed'])->default('scheduled');
            $table->decimal('distance_km', 8, 3)->nullable();
            $table->unsignedTinyInteger('pricing_tier')->nullable();
            $table->timestamps();

            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('child_id')
                ->references('id')
                ->on('children')
                ->onDelete('cascade');

            $table->unique(['driver_id', 'child_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
