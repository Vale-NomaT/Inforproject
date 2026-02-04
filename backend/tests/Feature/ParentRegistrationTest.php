<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_register_with_simplified_form(): void
    {
        $response = $this->post('/register/parent', [
            'name' => 'Test',
            'surname' => 'Parent',
            'email' => 'testparent@example.com',
            'phone' => '1234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('parent.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'testparent@example.com',
            'name' => 'Test Parent',
            'user_type' => 'parent',
        ]);

        $user = User::where('email', 'testparent@example.com')->first();

        $this->assertDatabaseHas('parents', [
            'id' => $user->id,
            'phone' => '1234567890',
            'relationship_to_child' => 'Parent',
        ]);
    }
}
