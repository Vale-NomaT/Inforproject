<?php

namespace App\Services;

use App\Models\Location;
use App\Models\School;

class PricingService
{
    protected DistanceService $distanceService;

    public function __construct(DistanceService $distanceService)
    {
        $this->distanceService = $distanceService;
    }

    /**
     * Determine the pricing tier and amount based on distance.
     *
     * @param Location $location
     * @param School $school
     * @return array{tier: int, price: int, distance_km: float}
     */
    public function determinePricing(Location $location, School $school): array
    {
        try {
            // Get round trip distance (since drivers do return trips or logic might imply daily commute)
            // But usually pricing is per month or per trip?
            // User prompt says: "$28 or $45 based on route distance"
            // Let's assume standard monthly fee or similar.
            
            // We use one-way distance for tier determination usually, but DistanceService returns round trip?
            // DistanceService::getRoundTripDistanceKm returns one_way * 2.
            
            $roundTripKm = $this->distanceService->getRoundTripDistanceKm($location, $school);
            $oneWayKm = $roundTripKm / 2;

            // Pricing Logic:
            // <= 10km one-way (or some threshold) -> $28
            // > 10km one-way -> $45
            
            if ($oneWayKm <= 10) {
                return [
                    'tier' => 1,
                    'price' => 28,
                    'distance_km' => $oneWayKm
                ];
            } else {
                return [
                    'tier' => 2,
                    'price' => 45,
                    'distance_km' => $oneWayKm
                ];
            }
        } catch (\Exception $e) {
            // Fallback if distance calc fails (e.g. API down)
            // Default to higher tier or handle error.
            // For now, defaulting to Tier 2 to be safe or maybe Tier 1?
            // Let's default to Tier 2 ($45) if distance is unknown.
            return [
                'tier' => 2,
                'price' => 45,
                'distance_km' => 0
            ];
        }
    }
}
