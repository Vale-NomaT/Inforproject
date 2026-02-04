<?php

namespace Tests\Unit;

use App\Models\Child;
use App\Models\DriverLocation;
use App\Models\DriverProfile;
use App\Models\DriverSchool;
use App\Models\Location;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\User;
use App\Services\DriverMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverMatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_only_drivers_serving_child_location_and_school(): void
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
        ]);

        $zoneB = Location::create([
            'name' => 'Zone B',
            'city' => 'City',
            'country' => 'Country',
        ]);

        $schoolX = School::create([
            'name' => 'School X',
            'city' => 'City',
            'country' => 'Country',
        ]);

        $schoolY = School::create([
            'name' => 'School Y',
            'city' => 'City',
            'country' => 'Country',
        ]);

        $child = Child::create([
            'parent_id' => $parentUser->id,
            'first_name' => 'Child',
            'last_name' => 'One',
            'date_of_birth' => '2015-01-01',
            'school_id' => $schoolX->id,
            'pickup_location_id' => $zoneA->id,
        ]);

        $matchingDriverUser = User::create([
            'name' => 'Driver Match',
            'email' => 'driver.match@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        DriverProfile::create([
            'id' => $matchingDriverUser->id,
            'license_number' => 'LIC-MATCH',
            'max_child_capacity' => 4,
        ]);

        DriverLocation::create([
            'driver_id' => $matchingDriverUser->id,
            'location_id' => $zoneA->id,
        ]);

        DriverSchool::create([
            'driver_id' => $matchingDriverUser->id,
            'school_id' => $schoolX->id,
        ]);

        $locationOnlyDriverUser = User::create([
            'name' => 'Driver Location Only',
            'email' => 'driver.location@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        DriverProfile::create([
            'id' => $locationOnlyDriverUser->id,
            'license_number' => 'LIC-LOC',
            'max_child_capacity' => 4,
        ]);

        DriverLocation::create([
            'driver_id' => $locationOnlyDriverUser->id,
            'location_id' => $zoneA->id,
        ]);

        DriverSchool::create([
            'driver_id' => $locationOnlyDriverUser->id,
            'school_id' => $schoolY->id,
        ]);

        $schoolOnlyDriverUser = User::create([
            'name' => 'Driver School Only',
            'email' => 'driver.school@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        DriverProfile::create([
            'id' => $schoolOnlyDriverUser->id,
            'license_number' => 'LIC-SCH',
            'max_child_capacity' => 4,
        ]);

        DriverLocation::create([
            'driver_id' => $schoolOnlyDriverUser->id,
            'location_id' => $zoneB->id,
        ]);

        DriverSchool::create([
            'driver_id' => $schoolOnlyDriverUser->id,
            'school_id' => $schoolX->id,
        ]);

        $pendingDriverUser = User::create([
            'name' => 'Driver Pending',
            'email' => 'driver.pending@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'pending',
        ]);

        DriverProfile::create([
            'id' => $pendingDriverUser->id,
            'license_number' => 'LIC-PEND',
            'max_child_capacity' => 4,
        ]);

        DriverLocation::create([
            'driver_id' => $pendingDriverUser->id,
            'location_id' => $zoneA->id,
        ]);

        DriverSchool::create([
            'driver_id' => $pendingDriverUser->id,
            'school_id' => $schoolX->id,
        ]);

        $service = new DriverMatchingService;

        $drivers = $service->findDriversForChild($child);

        $this->assertCount(1, $drivers);
        $this->assertTrue($drivers->contains('id', $matchingDriverUser->id));
    }
}
