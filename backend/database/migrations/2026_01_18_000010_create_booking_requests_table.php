<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('child_id');
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();

            $table->foreign('parent_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('child_id')
                ->references('id')
                ->on('children')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
