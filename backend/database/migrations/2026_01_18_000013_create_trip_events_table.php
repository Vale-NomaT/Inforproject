<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id');
            $table->enum('type', ['started', 'arrived', 'picked_up', 'dropped_off']);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('trip_id')
                ->references('id')
                ->on('trips')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_events');
    }
};
