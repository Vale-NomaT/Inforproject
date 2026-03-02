<?php

namespace Tests\Feature;

use App\Events\TripLocationUpdated;
use App\Models\Child;
use App\Models\Location;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ParentLiveTripTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_update_location_and_it_is_cached(): void
    {
        Event::fake();
        Cache::shouldReceive('put')->once();

        $driverUser = User::create([
            'name' => 'Driver One',
            'email' => 'driver@example.com',
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

        $child = Child::create([
            'parent_id' => $parentUser->id,
            'first_name' => 'Child',
            'last_name' => 'One',
            'date_of_birth' => '2015-01-01',
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
        ]);

        $trip = Trip::create([
            'driver_id' => $driverUser->id,
            'child_id' => $child->id,
            'scheduled_date' => now()->toDateString(),
            'status' => Trip::STATUS_IN_PROGRESS,
        ]);

        $this->actingAs($driverUser);

        $response = $this->postJson(route('driver.location.update'), [
            'lat' => 12.345678,
            'lng' => 98.765432,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        Event::assertDispatched(TripLocationUpdated::class);
    }

    public function test_parent_can_fetch_cached_location(): void
    {
        $driverUser = User::create([
            'name' => 'Driver One',
            'email' => 'driver@example.com',
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

        $child = Child::create([
            'parent_id' => $parentUser->id,
            'first_name' => 'Child',
            'last_name' => 'One',
            'date_of_birth' => '2015-01-01',
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
        ]);

        $trip = Trip::create([
            'driver_id' => $driverUser->id,
            'child_id' => $child->id,
            'scheduled_date' => now()->toDateString(),
            'status' => Trip::STATUS_IN_PROGRESS,
        ]);

        // Manually put data in cache
        Cache::put("trip_location_{$trip->id}", [
            'lat' => 12.345678,
            'lng' => 98.765432,
            'updated_at' => now()->timestamp,
        ], 300);

        $this->actingAs($parentUser);

        $response = $this->getJson(route('parent.trips.location', $trip));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'lat' => 12.345678,
            'lng' => 98.765432,
        ]);
    }

    public function test_unauthorized_user_cannot_fetch_location(): void
    {
        $otherParentUser = User::create([
            'name' => 'Parent Two',
            'email' => 'parent2@example.com',
            'password' => 'password',
            'user_type' => 'parent',
            'status' => 'active',
        ]);

        ParentProfile::create([
            'id' => $otherParentUser->id,
            'relationship_to_child' => 'father',
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

        $child = Child::create([
            'parent_id' => $parentUser->id,
            'first_name' => 'Child',
            'last_name' => 'One',
            'date_of_birth' => '2015-01-01',
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
        ]);

        $trip = Trip::create([
            'driver_id' => 1, // Dummy driver
            'child_id' => $child->id,
            'scheduled_date' => now()->toDateString(),
            'status' => Trip::STATUS_IN_PROGRESS,
        ]);

        $this->actingAs($otherParentUser);

        $response = $this->getJson(route('parent.trips.location', $trip));

        $response->assertStatus(403);
    }
}
