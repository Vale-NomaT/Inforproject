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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DriverTripEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_log_events_and_parent_is_notified(): void
    {
        Config::set('services.resend.key', 'test-resend-key');
        Http::fake();
        Event::fake([TripEventBroadcasted::class]);

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
            'status' => Trip::STATUS_IN_PROGRESS,
        ]);

        $this->actingAs($driverUser);

        $response = $this->postJson(route('driver.trips.events', ['trip' => $trip->id]), [
            'type' => 'arrived',
            'lat' => 10.1,
            'lng' => 20.2,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('trip_events', [
            'trip_id' => $trip->id,
            'type' => 'arrived',
            'lat' => 10.1,
            'lng' => 20.2,
        ]);

        Event::assertDispatched(TripEventBroadcasted::class);
    }
}
