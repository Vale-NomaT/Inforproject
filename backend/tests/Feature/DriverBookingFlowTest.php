<?php

namespace Tests\Feature;

use App\Events\BookingStatusUpdated;
use App\Models\BookingRequest;
use App\Models\Child;
use App\Models\DriverLocation;
use App\Models\DriverProfile;
use App\Models\DriverSchool;
use App\Models\Location;
use App\Models\ParentProfile;
use App\Models\RouteDistance;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DriverBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_approve_booking_and_parent_is_notified(): void
    {
        Config::set('services.openrouteservice.key', 'test-key');
        Config::set('services.resend.key', 'test-resend-key');

        Http::fake();
        Event::fake();

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
            'lat' => 10.00000000,
            'lng' => 20.00000000,
        ]);

        $schoolX = School::create([
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
            'school_id' => $schoolX->id,
            'pickup_location_id' => $zoneA->id,
        ]);

        RouteDistance::create([
            'location_id' => $zoneA->id,
            'school_id' => $schoolX->id,
            'one_way_distance_km' => 20.0,
            'last_calculated' => now(),
        ]);

        $driverUser = User::create([
            'name' => 'Driver One',
            'email' => 'driver@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        DriverProfile::create([
            'id' => $driverUser->id,
            'license_number' => 'LIC-ONE',
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Camry',
            'vehicle_color' => 'Blue',
            'max_child_capacity' => 4,
        ]);

        DriverLocation::create([
            'driver_id' => $driverUser->id,
            'location_id' => $zoneA->id,
        ]);

        DriverSchool::create([
            'driver_id' => $driverUser->id,
            'school_id' => $schoolX->id,
        ]);

        $this->actingAs($parentUser);

        $this->post(route('parent.children.drivers.store', ['child' => $child->id]), [
            'driver_id' => $driverUser->id,
        ]);

        $booking = BookingRequest::first();

        $this->actingAs($driverUser);

        $indexResponse = $this->get(route('driver.bookings.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Pending Booking Requests');
        $indexResponse->assertSee('Child One');

        $approveResponse = $this->post(route('driver.bookings.approve', ['booking' => $booking->id]));
        $approveResponse->assertRedirect();

        $this->assertDatabaseHas('booking_requests', [
            'id' => $booking->id,
            'status' => BookingRequest::STATUS_APPROVED,
        ]);

        Event::assertDispatched(BookingStatusUpdated::class);
    }
}
