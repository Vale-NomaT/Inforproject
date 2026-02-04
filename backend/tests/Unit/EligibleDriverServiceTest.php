<?php

namespace Tests\Unit;

use App\Models\Child;
use App\Models\DriverLocation;
use App\Models\DriverProfile;
use App\Models\DriverSchool;
use App\Models\Location;
use App\Models\ParentProfile;
use App\Models\RouteDistance;
use App\Models\School;
use App\Models\User;
use App\Services\DistanceService;
use App\Services\DriverMatchingService;
use App\Services\EligibleDriverService;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EligibleDriverServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_matching_drivers_with_same_pricing_and_excludes_others(): void
    {
        $parentUser = User::create([
            'name' => 'Parent One',
            'email' => 'parent@example.com',
            'password' => 'password',
            'user_type' => 'parent',
            'status' => 'active',
        ]);

        ParentProfile::create([
            'id' => $parentUser->id,
            'relationship_to_child' => 'mother',
        ]);

        $zoneA = Location::create([
            'name' => 'Zone A',
            'city' => 'City',
            'country' => 'Country',
            'lat' => 10.00000000,
            'lng' => 20.00000000,
        ]);

        $zoneB = Location::create([
            'name' => 'Zone B',
            'city' => 'City',
            'country' => 'Country',
            'lat' => 30.00000000,
            'lng' => 40.00000000,
        ]);

        $schoolX = School::create([
            'name' => 'School X',
            'city' => 'City',
            'country' => 'Country',
            'lat' => 11.00000000,
            'lng' => 21.00000000,
        ]);

        $schoolY = School::create([
            'name' => 'School Y',
            'city' => 'City',
            'country' => 'Country',
            'lat' => 31.00000000,
            'lng' => 41.00000000,
        ]);

        $child = Child::create([
            'parent_id' => $parentUser->id,
            'first_name' => 'Child',
            'last_name' => 'One',
            'date_of_birth' => '2015-01-01',
            'school_id' => $schoolX->id,
            'pickup_location_id' => $zoneA->id,
        ]);

        RouteDistance::create([
            'location_id' => $zoneA->id,
            'school_id' => $schoolX->id,
            'one_way_distance_km' => 20.0,
            'last_calculated' => now(),
        ]);

        $driverUserOne = User::create([
            'name' => 'Driver One',
            'email' => 'driver1@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        DriverProfile::create([
            'id' => $driverUserOne->id,
            'license_number' => 'LIC-ONE',
            'max_child_capacity' => 4,
        ]);

        DriverLocation::create([
            'driver_id' => $driverUserOne->id,
            'location_id' => $zoneA->id,
        ]);

        DriverSchool::create([
            'driver_id' => $driverUserOne->id,
            'school_id' => $schoolX->id,
        ]);

        $driverUserTwo = User::create([
            'name' => 'Driver Two',
            'email' => 'driver2@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        DriverProfile::create([
            'id' => $driverUserTwo->id,
            'license_number' => 'LIC-TWO',
            'max_child_capacity' => 4,
        ]);

        DriverLocation::create([
            'driver_id' => $driverUserTwo->id,
            'location_id' => $zoneA->id,
        ]);

        DriverSchool::create([
            'driver_id' => $driverUserTwo->id,
            'school_id' => $schoolX->id,
        ]);

        $nonMatchingDriverUser = User::create([
            'name' => 'Driver Non Match',
            'email' => 'drivernon@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        DriverProfile::create([
            'id' => $nonMatchingDriverUser->id,
            'license_number' => 'LIC-NON',
            'max_child_capacity' => 4,
        ]);

        DriverLocation::create([
            'driver_id' => $nonMatchingDriverUser->id,
            'location_id' => $zoneB->id,
        ]);

        DriverSchool::create([
            'driver_id' => $nonMatchingDriverUser->id,
            'school_id' => $schoolY->id,
        ]);

        $eligibleDriverService = new EligibleDriverService(
            new DriverMatchingService,
            new PricingService(new DistanceService)
        );

        $results = $eligibleDriverService->getEligibleDriversWithPricing($child);

        $this->assertCount(2, $results);

        foreach ($results as $result) {
            $this->assertSame(2, $result['tier']);
            $this->assertSame(45, $result['price']);
        }

        $driverIds = $results->pluck('driver.id')->all();

        $this->assertContains($driverUserOne->id, $driverIds);
        $this->assertContains($driverUserTwo->id, $driverIds);
        $this->assertNotContains($nonMatchingDriverUser->id, $driverIds);
    }
}
