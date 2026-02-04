<?php

namespace App\Services;

use App\Models\Child;
use App\Models\DriverPerformanceScore;
use Illuminate\Database\Eloquent\Collection;

class EligibleDriverService
{
    protected DriverMatchingService $driverMatchingService;

    protected PricingService $pricingService;

    public function __construct(
        DriverMatchingService $driverMatchingService,
        PricingService $pricingService
    ) {
        $this->driverMatchingService = $driverMatchingService;
        $this->pricingService = $pricingService;
    }

    public function getEligibleDriversWithPricing(Child $child): Collection
    {
        $drivers = $this->driverMatchingService->findDriversForChild($child);

        if ($drivers->isEmpty()) {
            return new Collection;
        }

        $location = $child->pickupLocation;
        $school = $child->school;

        if (! $location || ! $school) {
            return new Collection;
        }

        $pricing = $this->pricingService->determinePricing($location, $school);

        $scores = DriverPerformanceScore::whereIn('driver_id', $drivers->pluck('id'))->get()->keyBy('driver_id');

        // Eager load booking requests and user to optimize query
        $drivers->load(['bookingRequests' => function ($query) {
            $query->where('status', 'approved');
        }, 'user']);

        return new Collection($drivers->map(function ($driver) use ($pricing, $scores) {
            $score = $scores->get($driver->id);
            $occupiedSeats = $driver->bookingRequests->count();
            $freeSeats = max(0, $driver->max_child_capacity - $occupiedSeats);

            return [
                'driver' => $driver,
                'tier' => $pricing['tier'],
                'price' => $pricing['price'],
                'performance_score' => $score ? $score->score : null,
                'free_seats' => $freeSeats,
            ];
        })->all());
    }
}
