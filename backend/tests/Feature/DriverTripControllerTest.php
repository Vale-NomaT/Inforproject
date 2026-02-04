<?php

namespace Tests\Feature;

use App\Events\TripEventBroadcasted;
use App\Models\Child;
use App\Models\Location;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DriverTripControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_sees_only_own_trips_and_can_start_trip(): void
    {
        Event::fake();

        $driverUser = User::create([
            'name' => 'Driver One',
            'email' => 'driver@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        $otherDriverUser = User::create([
            'name' => 'Driver Two',
            'email' => 'driver2@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

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

        $childOne = Child::create([
            'parent_id' => $parentUser->id,
            'first_name' => 'Child',
            'last_name' => 'One',
            'date_of_birth' => '2015-01-01',
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
        ]);

        $childTwo = Child::create([
            'parent_id' => $parentUser->id,
            'first_name' => 'Child',
            'last_name' => 'Two',
            'date_of_birth' => '2016-01-01',
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
        ]);

        $tripForDriver = Trip::create([
            'driver_id' => $driverUser->id,
            'child_id' => $childOne->id,
            'scheduled_date' => now()->toDateString(),
            'status' => Trip::STATUS_SCHEDULED,
        ]);

        Trip::create([
            'driver_id' => $otherDriverUser->id,
            'child_id' => $childTwo->id,
            'scheduled_date' => now()->toDateString(),
            'status' => Trip::STATUS_SCHEDULED,
        ]);

        $this->actingAs($driverUser);

        $indexResponse = $this->get(route('driver.trips.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Child One');
        $indexResponse->assertDontSee('Child Two');

        $startResponse = $this->post(route('driver.trips.start', ['trip' => $tripForDriver->id]));
        $startResponse->assertRedirect();

        $this->assertDatabaseHas('trips', [
            'id' => $tripForDriver->id,
            'status' => Trip::STATUS_IN_PROGRESS,
        ]);

        $this->assertDatabaseHas('trip_events', [
            'trip_id' => $tripForDriver->id,
            'type' => 'started',
        ]);

        Event::assertDispatched(TripEventBroadcasted::class);
    }
}
