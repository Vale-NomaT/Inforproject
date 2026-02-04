<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookingRequest;
use App\Models\User;
use App\Models\Child;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $driverA = User::where('email', 'driver.a@example.com')->first();
        $driverB = User::where('email', 'driver.b@example.com')->first();
        $sarah = User::where('email', 'sarah@example.com')->first();
        
        $emma = Child::where('first_name', 'Emma')->first();
        $liam = Child::where('first_name', 'Liam')->first();

        if ($driverA && $sarah && $emma) {
            BookingRequest::create([
                'parent_id' => $sarah->id,
                'driver_id' => $driverA->id,
                'child_id' => $emma->id,
                'status' => BookingRequest::STATUS_APPROVED,
                'pricing_tier' => 1,
                'created_at' => now()->subDays(10),
                'responded_at' => now()->subDays(9),
            ]);
        }

        if ($driverB && $sarah && $liam) {
            BookingRequest::create([
                'parent_id' => $sarah->id,
                'driver_id' => $driverB->id,
                'child_id' => $liam->id,
                'status' => BookingRequest::STATUS_APPROVED,
                'pricing_tier' => 1,
                'created_at' => now()->subDays(10),
                'responded_at' => now()->subDays(9),
            ]);
        }
    }
}
