<?php

namespace App\Console\Commands;

use App\Models\DriverPerformanceScore;
use App\Models\Rating;
use App\Models\Trip;
use App\Models\TripEvent;
use App\Models\User;
use Illuminate\Console\Command;

class CalculateDriverPerformanceScores extends Command
{
    protected $signature = 'scores:calculate-driver-performance';

    protected $description = 'Calculate performance scores for drivers based on ratings and trips';

    public function handle(): int
    {
        $this->info('Starting driver performance score calculation...');
        $drivers = User::where('user_type', 'driver')->get();
        $this->info("Found {$drivers->count()} drivers to process.");

        foreach ($drivers as $driver) {
            $tripQuery = Trip::where('driver_id', $driver->id);

            $assignedTrips = $tripQuery->count();
            $completedTrips = (clone $tripQuery)->where('status', Trip::STATUS_COMPLETED)->count();

            $avgRating = Rating::where('driver_id', $driver->id)->avg('rating') ?: 0.0;

            $reliability = $assignedTrips > 0 ? $completedTrips / $assignedTrips : 0.0;

            $tripIds = $tripQuery->pluck('id');

            $tripsWithArrival = 0;

            if ($tripIds->isNotEmpty()) {
                $tripsWithArrival = TripEvent::whereIn('trip_id', $tripIds)
                    ->where('type', 'arrived')
                    ->distinct('trip_id')
                    ->count('trip_id');
            }

            $punctuality = $assignedTrips > 0 ? $tripsWithArrival / $assignedTrips : 0.0;

            $normalizedRating = $avgRating > 0 ? $avgRating / 5.0 : 0.0;

            $scoreFraction = (0.6 * $normalizedRating) + (0.2 * $reliability) + (0.2 * $punctuality);

            $score = round($scoreFraction * 100, 2);

            DriverPerformanceScore::updateOrCreate(
                [
                    'driver_id' => $driver->id,
                ],
                [
                    'score' => $score,
                    'avg_rating' => $avgRating,
                    'reliability' => $reliability,
                    'punctuality' => $punctuality,
                    'calculated_at' => now(),
                ]
            );
            
            $this->info("Updated score for driver ID {$driver->id}: {$score}");
        }

        $this->info('Driver performance score calculation completed.');
        return Command::SUCCESS;
    }
}
