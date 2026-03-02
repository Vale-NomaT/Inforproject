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

class DriverRunCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_run_status_updates_correctly()
    {
        $driver = User::factory()->create(['user_type' => 'driver']);
        DriverProfile::factory()->create(['id' => $driver->id]);

        $parent = User::factory()->create(['user_type' => 'parent']);
        ParentProfile::factory()->create(['id' => $parent->id]);

        $school = School::factory()->create();
        $location = Location::factory()->create();
        
        $child1 = Child::factory()->create([
            'parent_id' => $parent->id,
            'school_id' => $school->id,
            'pickup_location_id' => $location->id
        ]);
        
        $child2 = Child::factory()->create([
            'parent_id' => $parent->id,
            'school_id' => $school->id,
            'pickup_location_id' => $location->id
        ]);

        // Create two trips for the same run (Morning, Today)
        $trip1 = Trip::factory()->create([
            'driver_id' => $driver->id,
            'child_id' => $child1->id,
            'type' => 'morning',
            'scheduled_date' => now(),
            'status' => Trip::STATUS_IN_PROGRESS,
        ]);

        $trip2 = Trip::factory()->create([
            'driver_id' => $driver->id,
            'child_id' => $child2->id,
            'type' => 'morning',
            'scheduled_date' => now(),
            'status' => Trip::STATUS_IN_PROGRESS,
        ]);

        // 1. Check initial status (Both In Progress -> Run In Progress)
        $response = $this->actingAs($driver)->get(route('driver.trips.index'));
        $response->assertStatus(200);
        
        // Extract runs from view data
        $runs = $response->viewData('runs');
        $this->assertNotEmpty($runs);
        $run = $runs[0];
        $this->assertEquals(Trip::STATUS_IN_PROGRESS, $run['status']);

        // 2. Complete first trip
        $trip1->update(['status' => Trip::STATUS_COMPLETED]);

        $response = $this->actingAs($driver)->get(route('driver.trips.index'));
        $runs = $response->viewData('runs');
        $run = $runs[0];
        // Should still be In Progress because trip2 is not done
        $this->assertEquals(Trip::STATUS_IN_PROGRESS, $run['status']);

        // 3. Complete second trip
        $trip2->update(['status' => Trip::STATUS_COMPLETED]);

        $response = $this->actingAs($driver)->get(route('driver.trips.index'));
        $runs = $response->viewData('runs');
        $run = $runs[0];
        // Now it should be Completed
        $this->assertEquals(Trip::STATUS_COMPLETED, $run['status']);
    }
}
