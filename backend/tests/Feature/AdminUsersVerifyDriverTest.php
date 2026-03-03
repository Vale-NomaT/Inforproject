<?php

namespace Tests\Feature;

use App\Models\DriverProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersVerifyDriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_users_page_shows_verify_for_pending_driver_and_approve_works(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin', 'status' => 'active']);

        $pendingDriverUser = User::factory()->create([
            'user_type' => 'driver',
            'status' => 'pending',
        ]);

        DriverProfile::factory()->create(['id' => $pendingDriverUser->id]);

        $this->actingAs($admin);

        $page = $this->get(route('admin.users.index', ['type' => 'driver']));
        $page->assertStatus(200);
        $page->assertSee('Verify');

        $response = $this->post(route('admin.drivers.approve', ['driver' => $pendingDriverUser->id]));
        $response->assertRedirect(route('admin.drivers.pending'));

        $this->assertDatabaseHas('users', [
            'id' => $pendingDriverUser->id,
            'status' => 'active',
        ]);
    }
}

