<?php

namespace Tests\Feature;

use App\Console\Commands\ScheduleTripsForNextSchoolDay;
use App\Models\BookingRequest;
use App\Models\Child;
use App\Models\Location;
use App\Models\ParentProfile;
use App\Models\RouteDistance;
use App\Models\School;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ScheduleTripsForNextSchoolDayTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_trips_for_approved_bookings_on_next_school_day(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 12, 8));

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

        RouteDistance::create([
            'location_id' => $location->id,
            'school_id' => $school->id,
            'one_way_distance_km' => 10.0,
            'last_calculated' => now(),
        ]);

        $booking = BookingRequest::create([
            'parent_id' => $parentUser->id,
            'driver_id' => $driverUser->id,
            'child_id' => $child->id,
            'status' => BookingRequest::STATUS_APPROVED,
            'pricing_tier' => 2,
            'created_at' => now(),
        ]);

        $this->artisan('trips:schedule-next-school-day')
            ->assertExitCode(ScheduleTripsForNextSchoolDay::SUCCESS);

        $this->assertDatabaseCount('trips', 1);

        $trip = Trip::first();

        $expectedDate = Carbon::tomorrow();

        $this->assertSame($booking->driver_id, $trip->driver_id);
        $this->assertSame($booking->child_id, $trip->child_id);
        $this->assertTrue($trip->scheduled_date->isSameDay($expectedDate));
        $this->assertSame(10.0, $trip->distance_km);
        $this->assertSame(2, $trip->pricing_tier);
    }
}
