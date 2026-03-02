<?php

namespace Tests\Feature;

use App\Events\TripEventBroadcasted;
use App\Models\Child;
use App\Models\DriverProfile;
use App\Models\ParentProfile;
use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripUpdateNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DriverTripEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_log_events_and_notifications_are_sent()
    {
        Event::fake([TripEventBroadcasted::class]);
        Notification::fake();

        $driver = User::factory()->create(['user_type' => 'driver']);
        DriverProfile::factory()->create(['id' => $driver->id]);

        $parent = User::factory()->create(['user_type' => 'parent']);
        ParentProfile::factory()->create(['id' => $parent->id]);

        $child = Child::factory()->create(['parent_id' => $parent->id]);
        
        $trip = Trip::factory()->create([
            'driver_id' => $driver->id,
            'child_id' => $child->id,
            'status' => Trip::STATUS_IN_PROGRESS,
            'scheduled_date' => now(),
        ]);

        // 1. Arrived at Pickup
        $response = $this->actingAs($driver)->postJson(route('driver.trips.events.store', $trip), [
            'type' => 'arrived',
            'lat' => -20.0,
            'lng' => 28.0,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trip_events', [
            'trip_id' => $trip->id,
            'type' => 'arrived',
        ]);

        Notification::assertSentTo(
            [$parent],
            TripUpdateNotification::class,
            function ($notification, $channels) {
                return str_contains($notification->message, 'arrived at the pickup');
            }
        );

        // 2. Picked Up
        $response = $this->actingAs($driver)->postJson(route('driver.trips.events.store', $trip), [
            'type' => 'picked_up',
        ]);
        $response->assertStatus(200);
        
        Notification::assertSentTo(
            [$parent],
            TripUpdateNotification::class,
            function ($notification) {
                return str_contains($notification->message, 'picked up');
            }
        );

        // 3. Dropped Off (Completes Trip)
        $response = $this->actingAs($driver)->postJson(route('driver.trips.events.store', $trip), [
            'type' => 'dropped_off',
        ]);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => Trip::STATUS_COMPLETED,
        ]);

        Notification::assertSentTo(
            [$parent],
            TripUpdateNotification::class,
            function ($notification) {
                return str_contains($notification->message, 'dropped off');
            }
        );
    }
}
