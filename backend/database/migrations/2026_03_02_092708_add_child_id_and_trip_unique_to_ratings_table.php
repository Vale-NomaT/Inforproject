<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('ratings') && ! Schema::hasColumn('ratings', 'child_id')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->unsignedBigInteger('child_id')->nullable()->after('trip_id');
            });

            DB::statement('UPDATE ratings SET child_id = (SELECT child_id FROM trips WHERE trips.id = ratings.trip_id) WHERE child_id IS NULL');

            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->foreign('child_id')
                        ->references('id')
                        ->on('children')
                        ->onDelete('cascade');
                });
            } catch (\Throwable) {
            }
        }

        if (Schema::hasTable('ratings')) {
            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->index('trip_id');
                });
            } catch (\Throwable) {
            }

            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->index('parent_id');
                });
            } catch (\Throwable) {
            }

            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->index('driver_id');
                });
            } catch (\Throwable) {
            }

            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->dropUnique(['trip_id', 'parent_id']);
                });
            } catch (\Throwable) {
            }

            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->unique('trip_id');
                });
            } catch (\Throwable) {
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ratings')) {
            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->dropUnique(['trip_id']);
                });
            } catch (\Throwable) {
            }

            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->unique(['trip_id', 'parent_id']);
                });
            } catch (\Throwable) {
            }

            if (Schema::hasColumn('ratings', 'child_id')) {
                try {
                    Schema::table('ratings', function (Blueprint $table) {
                        $table->dropForeign(['child_id']);
                    });
                } catch (\Throwable) {
                }

                try {
                    Schema::table('ratings', function (Blueprint $table) {
                        $table->dropColumn('child_id');
                    });
                } catch (\Throwable) {
                }
            }

            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->dropIndex(['trip_id']);
                });
            } catch (\Throwable) {
            }

            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->dropIndex(['parent_id']);
                });
            } catch (\Throwable) {
            }

            try {
                Schema::table('ratings', function (Blueprint $table) {
                    $table->dropIndex(['driver_id']);
                });
            } catch (\Throwable) {
            }
        }
    }
};
