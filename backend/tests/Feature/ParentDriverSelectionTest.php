<?php

namespace Tests\Feature;

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
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ParentDriverSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_view_and_request_drivers_for_child(): void
    {
        Config::set('services.openrouteservice.key', 'test-key');
        Config::set('services.resend.key', 'test-resend-key');

        Http::fake();
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

        $response = $this->get(route('parent.children.drivers.show', ['child' => $child->id]));

        $response->assertStatus(200);
        $response->assertSee('Drivers for Child One');
        $response->assertSee('Toyota');
        $response->assertSee('Camry');

        $postResponse = $this->post(route('parent.children.drivers.store', ['child' => $child->id]), [
            'driver_id' => $driverUser->id,
        ]);

        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('status', 'Request sent! Awaiting driver confirmation.');

        $this->assertDatabaseHas('booking_requests', [
            'parent_id' => $parentUser->id,
            'driver_id' => $driverUser->id,
            'child_id' => $child->id,
            'status' => BookingRequest::STATUS_PENDING,
        ]);
    }
}
