<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\DriverProfile;
use App\Models\Location;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Trip;
use App\Models\TripEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DriverTripCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_complete_trip()
    {
        Event::fake();

        $driver = User::factory()->create(['user_type' => 'driver']);
        DriverProfile::factory()->create(['id' => $driver->id]);

        $parent = User::factory()->create(['user_type' => 'parent']);
        ParentProfile::factory()->create(['id' => $parent->id]);

        $school = School::factory()->create();
        $location = Location::factory()->create();
        
        $child = Child::factory()->create([
            'parent_id' => $parent->id,
            'school_id' => $school->id,
            'pickup_location_id' => $location->id
        ]);

        $trip = Trip::factory()->create([
            'driver_id' => $driver->id,
            'child_id' => $child->id,
            'status' => Trip::STATUS_IN_PROGRESS,
        ]);

        $response = $this->actingAs($driver)->postJson(route('driver.trips.events', $trip), [
            'type' => 'dropped_off',
            'lat' => 12.345678,
            'lng' => 98.765432,
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => Trip::STATUS_COMPLETED,
        ]);

        $this->assertDatabaseHas('trip_events', [
            'trip_id' => $trip->id,
            'type' => 'dropped_off',
        ]);
    }
}
