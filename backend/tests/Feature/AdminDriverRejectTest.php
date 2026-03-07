<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DriverProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDriverRejectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reject_driver_application()
    {
        $admin = User::factory()->create([
            'user_type' => 'admin',
        ]);

        $driver = User::factory()->create([
            'user_type' => 'driver',
            'status' => 'pending',
        ]);

        DriverProfile::factory()->create([
            'id' => $driver->id,
        ]);

        $reason = 'Documents are blurry';

        $response = $this->actingAs($admin)->post(route('admin.drivers.reject', $driver), [
            'reason' => $reason,
        ]);

        $response->assertRedirect(route('admin.drivers.pending'));
        $response->assertSessionHas('status', 'Driver rejected.');

        $this->assertDatabaseHas('users', [
            'id' => $driver->id,
            'status' => 'rejected', // This should fail before the fix
            'status_reason' => $reason,
        ]);
    }
}
