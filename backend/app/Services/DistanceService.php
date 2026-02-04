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

        if (! $apiKey) {
            throw new RuntimeException('OpenRouteService API key is not configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
            'coordinates' => [
                [(float) $location->lng, (float) $location->lat],
                [(float) $school->lng, (float) $school->lat],
            ],
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Failed to contact routing service.');
        }

        $data = $response->json();

        $distanceMeters = $data['features'][0]['properties']['summary']['distance'] ?? null;

        if (! is_numeric($distanceMeters)) {
            throw new RuntimeException('Invalid routing response.');
        }

        $oneWayKm = ((float) $distanceMeters) / 1000;

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

        return $oneWayKm * 2;
    }
}
