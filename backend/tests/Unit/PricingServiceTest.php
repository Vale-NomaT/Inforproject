<?php

namespace Tests\Unit;

use App\Models\Location;
use App\Models\School;
use App\Services\DistanceService;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigns_tier_one_for_short_distance(): void
    {
        Config::set('services.openrouteservice.key', 'test-key');

        Http::fake([
            'https://api.openrouteservice.org/*' => Http::response([
                'features' => [
                    [
                        'properties' => [
                            'summary' => [
                                'distance' => 12000,
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $location = Location::create([
            'name' => 'Zone A',
            'city' => 'City',
            'country' => 'Country',
            'lat' => 10.00000000,
            'lng' => 20.00000000,
        ]);

        $school = School::create([
            'name' => 'School X',
            'city' => 'City',
            'country' => 'Country',
            'lat' => 11.00000000,
            'lng' => 21.00000000,
        ]);

        $pricingService = new PricingService(new DistanceService);

        $result = $pricingService->determinePricing($location, $school);

        $this->assertSame(1, $result['tier']);
        $this->assertSame(28, $result['price']);

        $this->assertDatabaseHas('route_distances', [
            'location_id' => $location->id,
            'school_id' => $school->id,
        ]);
    }

    public function test_assigns_tier_two_for_long_distance(): void
    {
        Config::set('services.openrouteservice.key', 'test-key');

        Http::fake([
            'https://api.openrouteservice.org/*' => Http::response([
                'features' => [
                    [
                        'properties' => [
                            'summary' => [
                                'distance' => 20000,
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $location = Location::create([
            'name' => 'Zone B',
            'city' => 'City',
            'country' => 'Country',
            'lat' => 30.00000000,
            'lng' => 40.00000000,
        ]);

        $school = School::create([
            'name' => 'School Y',
            'city' => 'City',
            'country' => 'Country',
            'lat' => 31.00000000,
            'lng' => 41.00000000,
        ]);

        $pricingService = new PricingService(new DistanceService);

        $result = $pricingService->determinePricing($location, $school);

        $this->assertSame(2, $result['tier']);
        $this->assertSame(45, $result['price']);

        $this->assertDatabaseHas('route_distances', [
            'location_id' => $location->id,
            'school_id' => $school->id,
        ]);
    }
}
