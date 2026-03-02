<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\DriverProfile;
use App\Models\ParentProfile;
use App\Models\Location;
use App\Models\Rating;
use App\Models\School;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_view_ratings_page()
    {
        $driver = User::factory()->create(['user_type' => 'driver']);
        DriverProfile::factory()->create(['id' => $driver->id]);

        $response = $this->actingAs($driver)->get(route('driver.ratings.index'));

        $response->assertStatus(200);
        $response->assertViewIs('driver.ratings');
        $response->assertViewHas(['ratings', 'averageRating', 'totalRatings']);
    }

    public function test_ratings_page_displays_correct_data()
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

        // Create 3 ratings: 5, 4, 3. Average should be 4.
        $trip1 = Trip::factory()->create(['driver_id' => $driver->id, 'child_id' => $child->id, 'status' => 'completed']);
        Rating::factory()->create(['trip_id' => $trip1->id, 'driver_id' => $driver->id, 'rating' => 5, 'comment' => 'Great!']);

        $trip2 = Trip::factory()->create(['driver_id' => $driver->id, 'child_id' => $child->id, 'status' => 'completed']);
        Rating::factory()->create(['trip_id' => $trip2->id, 'driver_id' => $driver->id, 'rating' => 4, 'comment' => 'Good']);

        $trip3 = Trip::factory()->create(['driver_id' => $driver->id, 'child_id' => $child->id, 'status' => 'completed']);
        Rating::factory()->create(['trip_id' => $trip3->id, 'driver_id' => $driver->id, 'rating' => 3, 'comment' => 'Okay']);

        $response = $this->actingAs($driver)->get(route('driver.ratings.index'));

        $response->assertStatus(200);
        $response->assertViewHas('averageRating', 4.0);
        $response->assertViewHas('totalRatings', 3);
        
        // Check if comments are visible
        $response->assertSee('Great!');
        $response->assertSee('Good');
        $response->assertSee('Okay');
    }

    public function test_ratings_page_handles_no_ratings()
    {
        $driver = User::factory()->create(['user_type' => 'driver']);
        DriverProfile::factory()->create(['id' => $driver->id]);

        $response = $this->actingAs($driver)->get(route('driver.ratings.index'));

        $response->assertStatus(200);
        $response->assertViewHas('averageRating', 0);
        $response->assertViewHas('totalRatings', 0);
        $response->assertSee('No reviews yet');
    }
}
