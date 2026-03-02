<?php

namespace App\Console\Commands;

use App\Models\DriverPerformanceScore;
use App\Models\Rating;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Console\Command;

class CalculateDriverPerformanceScores extends Command
{
    protected $signature = 'scores:calculate-driver-performance';

    protected $description = 'Calculate driver performance score (DPS) from rating, reliability, and punctuality';

    public function handle(): int
    {
        $this->info('Starting driver performance score calculation...');
        $drivers = User::where('user_type', 'driver')->get();
        $this->info("Found {$drivers->count()} drivers to process.");

        foreach ($drivers as $driver) {
            $assignedTrips = Trip::where('driver_id', $driver->id)->count();
            $completedTrips = Trip::where('driver_id', $driver->id)
                ->where('status', Trip::STATUS_COMPLETED)
                ->count();

            $avgRating = (float) (Rating::where('driver_id', $driver->id)->avg('rating') ?? 0);

            $reliability = $assignedTrips > 0 ? ($completedTrips / $assignedTrips) : 0.0;

            $onTimeTrips = Trip::where('driver_id', $driver->id)
                ->where('status', Trip::STATUS_COMPLETED)
                ->where('is_on_time', true)
                ->count();

            $punctuality = $completedTrips > 0 ? ($onTimeTrips / $completedTrips) : 0.0;

            $normalizedRating = ($avgRating / 5) * 100;
            $score = ($normalizedRating * 0.6)
                + (($reliability * 100) * 0.2)
                + (($punctuality * 100) * 0.2);
            $score = round($score, 2);

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
            
            $this->info("Updated score for driver ID {$driver->id}: ".number_format($score, 2));
        }

        $this->info('Driver performance score calculation completed.');
        return Command::SUCCESS;
    }
}
