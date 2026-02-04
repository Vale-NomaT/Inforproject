<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_safekid_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SafeRide Kids');
        $response->assertSee('Safe, trusted school transport for your child');
        $response->assertSee('I’m a Parent');
        $response->assertSee('I’m a Driver');
        $response->assertSee('Admin Login');
        $response->assertSee('For verified admins only');
    }

    public function test_authenticated_parent_is_redirected_to_parent_dashboard(): void
    {
        $user = User::factory()->create([
            'user_type' => 'parent',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('parent.dashboard'));
    }

    public function test_authenticated_driver_is_redirected_to_driver_dashboard(): void
    {
        $user = User::factory()->create([
            'user_type' => 'driver',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('driver.dashboard'));
    }

    public function test_authenticated_admin_is_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'user_type' => 'admin',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('admin.dashboard'));
    }
}
