<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $email = 'saferidek@gmail.com';
        // The user specifically requested password 'saferide'
        $password = Hash::make('saferide');

        // Check if user exists
        $user = DB::table('users')->where('email', $email)->first();

        if ($user) {
            // Update existing user
            DB::table('users')->where('email', $email)->update([
                'password' => $password,
                'user_type' => 'admin',
                'status' => 'active',
                'updated_at' => now(),
            ]);
        } else {
            // Create the user if they don't exist
            DB::table('users')->insert([
                'name' => 'Admin User',
                'email' => $email,
                'password' => $password,
                'user_type' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We generally don't want to revert this kind of administrative fix
    }
};
