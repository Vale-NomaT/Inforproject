<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_filter_users_and_suspend(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'user_type' => 'admin',
            'status' => 'active',
        ]);

        $parent = User::create([
            'name' => 'Parent One',
            'email' => 'parent@example.com',
            'password' => 'password',
            'user_type' => 'parent',
            'status' => 'active',
        ]);

        $driver = User::create([
            'name' => 'Driver One',
            'email' => 'driver@example.com',
            'password' => 'password',
            'user_type' => 'driver',
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Parent One');
        $response->assertSee('Driver One');

        $filterResponse = $this->get(route('admin.users.index', ['type' => 'driver']));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Driver One');
        $filterResponse->assertDontSee('Parent One');

        $suspendResponse = $this->post(route('admin.users.suspend', ['user' => $driver->id]), [
            'reason' => 'Violation of policy',
        ]);

        $suspendResponse->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $driver->id,
            'status' => 'suspended',
            'status_reason' => 'Violation of policy',
        ]);
    }
}
