<?php

namespace Tests\Feature;

use App\Models\DriverProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDriverManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_approve_pending_drivers(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'user_type' => 'admin',
            'status' => 'active',
        ]);

        $driverPending = User::create([
            'name' => 'Pending Driver',
            'email' => 'driver1@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'pending',
        ]);

        DriverProfile::create([
            'id' => $driverPending->id,
            'license_number' => 'LIC-PENDING',
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Corolla',
            'vehicle_color' => 'Blue',
            'max_child_capacity' => 4,
        ]);

        $driverActive = User::create([
            'name' => 'Active Driver',
            'email' => 'driver2@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.drivers.pending'));
        $response->assertStatus(200);
        $response->assertSee('Pending Driver');
        $response->assertDontSee('Active Driver');

        $approveResponse = $this->post(route('admin.drivers.approve', ['driver' => $driverPending->id]));
        $approveResponse->assertRedirect(route('admin.drivers.pending'));

        $this->assertDatabaseHas('users', [
            'id' => $driverPending->id,
            'status' => 'active',
            'status_reason' => null,
        ]);
    }

    public function test_admin_can_reject_pending_driver_with_reason(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'user_type' => 'admin',
            'status' => 'active',
        ]);

        $driverPending = User::create([
            'name' => 'Pending Driver',
            'email' => 'driver1@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        $rejectResponse = $this->post(route('admin.drivers.reject', ['driver' => $driverPending->id]), [
            'reason' => 'Incomplete documents',
        ]);

        $rejectResponse->assertRedirect(route('admin.drivers.pending'));

        $this->assertDatabaseHas('users', [
            'id' => $driverPending->id,
            'status' => 'suspended',
            'status_reason' => 'Incomplete documents',
        ]);
    }
}
