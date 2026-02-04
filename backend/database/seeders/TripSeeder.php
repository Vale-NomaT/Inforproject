<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\TripEvent;
use App\Models\Rating;
use App\Models\DriverPerformanceScore;
use App\Models\User;
use App\Models\Child;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $driverA = User::where('email', 'driver.a@example.com')->first();
        $driverB = User::where('email', 'driver.b@example.com')->first();
        $sarah = User::where('email', 'sarah@example.com')->first();
        
        $emma = Child::where('first_name', 'Emma')->first();
        $liam = Child::where('first_name', 'Liam')->first();

        // Trip 1: Emma with Driver A (3 days ago)
        if ($driverA && $emma) {
            $trip1 = Trip::create([
                'driver_id' => $driverA->id,
                'child_id' => $emma->id,
                'scheduled_date' => now()->subDays(3),
                'status' => Trip::STATUS_COMPLETED,
                'distance_km' => 12.0,
                'pricing_tier' => 1,
            ]);

            $this->createTripEvents($trip1);

            // Rating for Trip 1
            if ($sarah) {
                Rating::create([
                    'trip_id' => $trip1->id,
                    'parent_id' => $sarah->id,
                    'driver_id' => $driverA->id,
                    'rating' => 4,
                    'comment' => 'Good drive, slightly late.',
                ]);
            }
        }

        // Trip 2: Emma with Driver A (2 days ago)
        if ($driverA && $emma) {
            $trip2 = Trip::create([
                'driver_id' => $driverA->id,
                'child_id' => $emma->id,
                'scheduled_date' => now()->subDays(2),
                'status' => Trip::STATUS_COMPLETED,
                'distance_km' => 12.0,
                'pricing_tier' => 1,
            ]);

            $this->createTripEvents($trip2);
            
            // Rating for Trip 2
             if ($sarah) {
                Rating::create([
                    'trip_id' => $trip2->id,
                    'parent_id' => $sarah->id,
                    'driver_id' => $driverA->id,
                    'rating' => 5,
                    'comment' => 'Perfect timing today!',
                ]);
            }
        }

        // Trip 3: Liam with Driver B (1 day ago)
        if ($driverB && $liam) {
            $trip3 = Trip::create([
                'driver_id' => $driverB->id,
                'child_id' => $liam->id,
                'scheduled_date' => now()->subDays(1),
                'status' => Trip::STATUS_COMPLETED,
                'distance_km' => 8.5,
                'pricing_tier' => 1,
            ]);

            $this->createTripEvents($trip3);

             // Rating for Trip 3
             if ($sarah) {
                Rating::create([
                    'trip_id' => $trip3->id,
                    'parent_id' => $sarah->id,
                    'driver_id' => $driverB->id,
                    'rating' => 5,
                    'comment' => 'Excellent service.',
                ]);
            }
        }

        // Driver Performance Scores
        if ($driverA) {
            DriverPerformanceScore::create([
                'driver_id' => $driverA->id,
                'avg_rating' => 4.5,
                'punctuality' => 0.95,
                'reliability' => 0.98,
                'score' => 96.5,
                'calculated_at' => now(),
            ]);
        }

        if ($driverB) {
            DriverPerformanceScore::create([
                'driver_id' => $driverB->id,
                'avg_rating' => 5.0,
                'punctuality' => 1.0,
                'reliability' => 1.0,
                'score' => 100.0,
                'calculated_at' => now(),
            ]);
        }
    }

    private function createTripEvents(Trip $trip)
    {
        // Started
        TripEvent::create([
            'trip_id' => $trip->id,
            'type' => 'started',
            'lat' => 43.6532,
            'lng' => -79.3832,
            'created_at' => $trip->scheduled_date->copy()->setTime(8, 0, 0),
        ]);

        // Arrived at Pickup
        TripEvent::create([
            'trip_id' => $trip->id,
            'type' => 'arrived',
            'lat' => 43.6535,
            'lng' => -79.3835,
            'created_at' => $trip->scheduled_date->copy()->setTime(8, 10, 0),
        ]);

        // Picked Up
        TripEvent::create([
            'trip_id' => $trip->id,
            'type' => 'picked_up',
            'lat' => 43.6535,
            'lng' => -79.3835,
            'created_at' => $trip->scheduled_date->copy()->setTime(8, 15, 0),
        ]);

        // Dropped Off
        TripEvent::create([
            'trip_id' => $trip->id,
            'type' => 'dropped_off',
            'lat' => 43.6600,
            'lng' => -79.3500,
            'created_at' => $trip->scheduled_date->copy()->setTime(8, 40, 0),
        ]);
    }
}
