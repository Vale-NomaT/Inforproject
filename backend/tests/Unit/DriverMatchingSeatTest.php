<?php

namespace Tests\Unit;

use App\Models\BookingRequest;
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

class DriverMatchingSeatTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_excludes_drivers_with_no_available_seats(): void
    {
        // 1. Setup Data
        $zoneA = Location::create(['name' => 'Zone A', 'city' => 'City', 'country' => 'Country']);
        $schoolX = School::create(['name' => 'School X', 'city' => 'City', 'country' => 'Country']);

        // Child needing ride
        $parentUser = User::factory()->create(['user_type' => 'parent']);
        ParentProfile::create(['id' => $parentUser->id, 'relationship_to_child' => 'parent']);
        $child = Child::create([
            'parent_id' => $parentUser->id,
            'first_name' => 'Child',
            'last_name' => 'One',
            'date_of_birth' => '2015-01-01',
            'school_id' => $schoolX->id,
            'pickup_location_id' => $zoneA->id,
        ]);

        // Driver with 1 seat capacity, FULL
        $driverFullUser = User::factory()->create(['user_type' => 'driver', 'status' => 'active']);
        $driverFull = DriverProfile::create([
            'id' => $driverFullUser->id,
            'max_child_capacity' => 1,
            'license_number' => 'FULL',
        ]);
        DriverLocation::create(['driver_id' => $driverFullUser->id, 'location_id' => $zoneA->id]);
        DriverSchool::create(['driver_id' => $driverFullUser->id, 'school_id' => $schoolX->id]);
        
        // Create an APPROVED booking for this driver
        BookingRequest::create([
            'parent_id' => $parentUser->id,
            'driver_id' => $driverFullUser->id,
            'child_id' => $child->id, // Doesn't matter which child, just consumes a seat
            'status' => 'approved',
            'pricing_tier' => 1,
        ]);

        // Driver with 2 seats capacity, 1 occupied, AVAILABLE
        $driverAvailUser = User::factory()->create(['user_type' => 'driver', 'status' => 'active']);
        $driverAvail = DriverProfile::create([
            'id' => $driverAvailUser->id,
            'max_child_capacity' => 2,
            'license_number' => 'AVAIL',
        ]);
        DriverLocation::create(['driver_id' => $driverAvailUser->id, 'location_id' => $zoneA->id]);
        DriverSchool::create(['driver_id' => $driverAvailUser->id, 'school_id' => $schoolX->id]);

        BookingRequest::create([
            'parent_id' => $parentUser->id,
            'driver_id' => $driverAvailUser->id,
            'child_id' => $child->id,
            'status' => 'approved',
            'pricing_tier' => 1,
        ]);

        // 2. Run Matching
        $service = new DriverMatchingService;
        $drivers = $service->findDriversForChild($child);

        // 3. Assertions
        // Should only contain the available driver
        $this->assertFalse($drivers->contains('id', $driverFullUser->id), 'Full driver should not be matched');
        $this->assertTrue($drivers->contains('id', $driverAvailUser->id), 'Available driver should be matched');
    }
}
