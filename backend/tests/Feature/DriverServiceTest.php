<?php

namespace Tests\Feature;

use App\Models\DriverProfile;
use App\Models\Location;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DriverServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_update_service_area()
    {
        // Create a driver
        $user = User::factory()->create(['user_type' => 'driver']);
        $driverProfile = DriverProfile::factory()->create(['id' => $user->id]);

        // Create locations and schools
        $locations = Location::factory()->count(3)->create(['is_active' => true]);
        $schools = School::factory()->count(3)->create(['is_active' => true]);

        // Authenticate as driver
        $this->actingAs($user);

        // Select first 2 locations and first school
        $selectedLocations = $locations->take(2)->pluck('id')->toArray();
        $selectedSchools = $schools->take(1)->pluck('id')->toArray();

        $response = $this->put(route('driver.service.update'), [
            'locations' => $selectedLocations,
            'schools' => $selectedSchools,
        ]);

        $response->assertRedirect(route('driver.service.edit'));
        $response->assertSessionHas('success');

        // Verify database
        $this->assertCount(2, $driverProfile->locations);
        $this->assertCount(1, $driverProfile->schools);
        
        $this->assertTrue($driverProfile->locations->contains($locations[0]->id));
        $this->assertTrue($driverProfile->locations->contains($locations[1]->id));
        $this->assertTrue($driverProfile->schools->contains($schools[0]->id));
    }

    public function test_driver_can_clear_service_area()
    {
        // Create a driver with existing service area
        $user = User::factory()->create(['user_type' => 'driver']);
        $driverProfile = DriverProfile::factory()->create(['id' => $user->id]);

        $locations = Location::factory()->count(3)->create();
        $schools = School::factory()->count(3)->create();

        $driverProfile->locations()->attach($locations->pluck('id'));
        $driverProfile->schools()->attach($schools->pluck('id'));

        $this->assertCount(3, $driverProfile->locations);
        $this->assertCount(3, $driverProfile->schools);

        // Authenticate
        $this->actingAs($user);

        // Send empty arrays (simulating clearing all selections)
        // Note: In HTML forms, empty multi-selects might send nothing, so controller handles default []
        // But here we explicitly send empty arrays or nothing to test controller logic
        
        // Case 1: Send empty arrays explicitly
        $response = $this->put(route('driver.service.update'), [
            'locations' => [],
            'schools' => [],
        ]);

        $response->assertRedirect(route('driver.service.edit'));
        
        $driverProfile->refresh();
        $this->assertCount(0, $driverProfile->locations);
        $this->assertCount(0, $driverProfile->schools);
    }
    
    public function test_driver_cannot_select_invalid_ids()
    {
        $user = User::factory()->create(['user_type' => 'driver']);
        DriverProfile::factory()->create(['id' => $user->id]);

        $this->actingAs($user);

        $response = $this->put(route('driver.service.update'), [
            'locations' => [9999], // Invalid ID
            'schools' => [9999],   // Invalid ID
        ]);

        $response->assertSessionHasErrors(['locations.0', 'schools.0']);
    }
}
