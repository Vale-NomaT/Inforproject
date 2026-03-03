<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\DriverProfile;
use App\Models\Location;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverTripHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_view_trip_history()
    {
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

        $completedTrip = Trip::factory()->create([
            'driver_id' => $driver->id,
            'child_id' => $child->id,
            'status' => Trip::STATUS_COMPLETED,
            'scheduled_date' => now()->subDay(),
        ]);

        $scheduledTrip = Trip::factory()->create([
            'driver_id' => $driver->id,
            'child_id' => $child->id,
            'status' => Trip::STATUS_SCHEDULED,
            'scheduled_date' => now()->addDay(),
        ]);

        $response = $this->actingAs($driver)->get(route('driver.trips.history'));

        $response->assertStatus(200);
        $response->assertViewIs('driver.trip-history');
        $response->assertSee($completedTrip->child->first_name);
        $response->assertDontSee($scheduledTrip->scheduled_date->format('Y-m-d')); // Assuming view doesn't show scheduled trips
    }
}
