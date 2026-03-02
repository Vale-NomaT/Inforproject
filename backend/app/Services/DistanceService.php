<?php

namespace App\Services;

use App\Models\Location;
use App\Models\RouteDistance;
use App\Models\School;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DistanceService
{
    public function getRoundTripDistanceKm(Location $location, School $school): float
    {
        $cached = RouteDistance::where('location_id', $location->id)
            ->where('school_id', $school->id)
            ->first();

        if ($cached) {
            return $cached->one_way_distance_km * 2;
        }

        if ($location->lat === null || $location->lng === null || $school->lat === null || $school->lng === null) {
            throw new RuntimeException('Coordinates are required to calculate distance.');
        }

        $apiKey = Config::get('services.openrouteservice.key');

        // Try OpenRouteService if key is present
        if ($apiKey) {
            try {
                $response = Http::timeout(5)->withHeaders([
                    'Authorization' => $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
                    'coordinates' => [
                        [(float) $location->lng, (float) $location->lat],
                        [(float) $school->lng, (float) $school->lat],
                    ],
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $distanceMeters = $data['features'][0]['properties']['summary']['distance'] ?? null;

                    if (is_numeric($distanceMeters)) {
                        return $this->storeAndReturnDistance($location, $school, $distanceMeters);
                    }
                }
            } catch (\Exception $e) {
                // ORS failed, proceed to fallback
            }
        }

        // Fallback: OSRM (Open Source Routing Machine) Public API
        // No API key required for the public demo server, but rate limited.
        // Great for fallback when ORS key is missing or invalid.
        try {
            // OSRM format: {lon},{lat};{lon},{lat}
            $url = "http://router.project-osrm.org/route/v1/driving/{$location->lng},{$location->lat};{$school->lng},{$school->lat}?overview=false";
            
            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                // OSRM returns distance in meters in routes[0].distance
                $distanceMeters = $data['routes'][0]['distance'] ?? null;

                if (is_numeric($distanceMeters)) {
                    return $this->storeAndReturnDistance($location, $school, $distanceMeters);
                }
            }
        } catch (\Exception $e) {
            // OSRM failed
        }

        throw new RuntimeException('Unable to calculate driving distance via any routing service.');
    }

    /**
     * Helper to store distance and return round trip km
     */
    protected function storeAndReturnDistance(Location $location, School $school, float $distanceMeters): float
    {
        $oneWayKm = $distanceMeters / 1000;

        try {
            RouteDistance::updateOrCreate(
                [
                    'location_id' => $location->id,
                    'school_id' => $school->id,
                ],
                [
                    'one_way_distance_km' => $oneWayKm,
                    'last_calculated' => now(),
                ]
            );
        } catch (\Exception $e) {
            // If caching fails (e.g. FK constraint, DB issue), 
            // we should still return the calculated distance.
            // \Log::error('Failed to cache route distance: ' . $e->getMessage());
        }

        return $oneWayKm * 2;
    }
}
