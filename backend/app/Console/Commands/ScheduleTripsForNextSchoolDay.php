<?php

namespace App\Console\Commands;

use App\Models\BookingRequest;
use App\Models\Trip;
use App\Services\DistanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ScheduleTripsForNextSchoolDay extends Command
{
    protected $signature = 'trips:schedule-next-school-day';

    protected $description = 'Create trips for approved bookings for the upcoming week';

    public function handle(DistanceService $distanceService): int
    {
        // Generate trips for Today + Next 6 days (Total 7 days)
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(7);

        $bookings = BookingRequest::where('status', BookingRequest::STATUS_APPROVED)->get();

        $this->info("Scheduling trips from {$startDate->toDateString()} to {$endDate->toDateString()}");

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $this->scheduleForDate($date->copy(), $bookings, $distanceService);
        }

        return Command::SUCCESS;
    }

    protected function scheduleForDate(Carbon $date, $bookings, DistanceService $distanceService)
    {
        foreach ($bookings as $booking) {
            $exists = Trip::where('driver_id', $booking->driver_id)
                ->where('child_id', $booking->child_id)
                ->whereDate('scheduled_date', $date)
                ->exists();

            if ($exists) {
                continue;
            }

            $child = $booking->child;

            if (! $child || ! $child->pickupLocation || ! $child->school) {
                continue;
            }

            $distanceKm = null;

            try {
                $roundTrip = $distanceService->getRoundTripDistanceKm(
                    $child->pickupLocation,
                    $child->school
                );

                $distanceKm = $roundTrip / 2;
            } catch (\Throwable $e) {
                $distanceKm = null;
            }

            Trip::create([
                'driver_id' => $booking->driver_id,
                'child_id' => $booking->child_id,
                'scheduled_date' => $date->toDateString(),
                'status' => Trip::STATUS_SCHEDULED,
                'distance_km' => $distanceKm,
                'pricing_tier' => $booking->pricing_tier,
            ]);
            
            $this->info("Created trip for Child ID {$child->id} on {$date->toDateString()}");
        }
    }
}
