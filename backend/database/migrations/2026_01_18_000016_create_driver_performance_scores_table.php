<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_performance_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id')->unique();
            $table->decimal('score', 5, 2)->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->decimal('reliability', 5, 4)->default(0);
            $table->decimal('punctuality', 5, 4)->default(0);
            $table->timestamp('calculated_at')->nullable();

            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_performance_scores');
    }
};
