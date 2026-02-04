<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\Location;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentTripRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_rate_completed_trip(): void
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

        $driverUser = User::create([
            'name' => 'Driver One',
            'email' => 'driver@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
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
            'status' => Trip::STATUS_COMPLETED,
        ]);

        $this->actingAs($parentUser);

        $getResponse = $this->get(route('parent.trips.rate.create', ['trip' => $trip->id]));
        $getResponse->assertStatus(200);
        $getResponse->assertSee('Rate Your Driver');

        $postResponse = $this->post(route('parent.trips.rate.store', ['trip' => $trip->id]), [
            'rating' => 5,
            'comment' => 'Great trip',
        ]);

        $postResponse->assertRedirect(route('parent.dashboard'));

        $this->assertDatabaseHas('ratings', [
            'trip_id' => $trip->id,
            'driver_id' => $driverUser->id,
            'parent_id' => $parentUser->id,
            'rating' => 5,
            'comment' => 'Great trip',
        ]);
    }
}
