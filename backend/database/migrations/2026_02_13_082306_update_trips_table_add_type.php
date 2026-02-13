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
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropUnique(['driver_id', 'child_id', 'scheduled_date']);
            
            $table->enum('type', ['morning', 'afternoon'])->default('morning')->after('scheduled_date');
            $table->unique(['driver_id', 'child_id', 'scheduled_date', 'type']);
            
            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropUnique(['driver_id', 'child_id', 'scheduled_date', 'type']);
            
            $table->dropColumn('type');
            $table->unique(['driver_id', 'child_id', 'scheduled_date']);
            
            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
