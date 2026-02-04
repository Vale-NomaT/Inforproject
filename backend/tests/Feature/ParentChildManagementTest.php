<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParentChildManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_add_child_with_new_fields()
    {
        // Create a parent user
        $parent = User::factory()->create([
            'user_type' => 'parent',
        ]);
        
        // Create parent profile
        \App\Models\ParentProfile::factory()->create([
            'id' => $parent->id,
        ]);

        // Create school and location
        $school = School::factory()->create();
        $location = Location::factory()->create();

        // Authenticate as parent
        $response = $this->actingAs($parent);

        // Data for new child
        $childData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => now()->subYears(5)->format('Y-m-d'),
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
            'relationship' => 'Father',
            'school_start_time' => '08:00',
            'school_end_time' => '14:00',
            'medical_notes' => 'None',
        ];

        // Submit form
        $response = $this->post(route('parent.children.store'), $childData);

        // Assert redirect and session message
        $response->assertRedirect(route('parent.dashboard'));
        $response->assertSessionHas('status', 'Child added successfully!');

        // Assert database has child with new fields
        $this->assertDatabaseHas('children', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'parent_id' => $parent->id,
            'relationship' => 'Father',
            'school_start_time' => '08:00',
            'school_end_time' => '14:00',
        ]);
    }

    public function test_parent_can_edit_child_details()
    {
        // Create a parent user
        $parent = User::factory()->create([
            'user_type' => 'parent',
        ]);
        
        \App\Models\ParentProfile::factory()->create([
            'id' => $parent->id,
        ]);

        // Create school and location
        $school = School::factory()->create();
        $location = Location::factory()->create();

        // Create a child
        $child = \App\Models\Child::create([
            'parent_id' => $parent->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => now()->subYears(5)->format('Y-m-d'),
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
            'relationship' => 'Father',
            'school_start_time' => '08:00',
            'school_end_time' => '14:00',
            'medical_notes' => 'None',
        ]);

        // Authenticate as parent
        $this->actingAs($parent);

        // Updated data
        $updatedData = [
            'first_name' => 'Johnny',
            'last_name' => 'Doe',
            'date_of_birth' => now()->subYears(6)->format('Y-m-d'),
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
            'relationship' => 'Father',
            'school_start_time' => '08:30',
            'school_end_time' => '14:30',
            'medical_notes' => 'Updated notes',
        ];

        // Submit update request
        $response = $this->put(route('parent.children.update', $child->id), $updatedData);

        // Assert redirect and session message
        $response->assertRedirect(route('parent.dashboard'));
        $response->assertSessionHas('status', 'Child details updated successfully!');

        // Assert database has updated child
        $this->assertDatabaseHas('children', [
            'id' => $child->id,
            'first_name' => 'Johnny',
            'school_start_time' => '08:30',
            'school_end_time' => '14:30',
            'medical_notes' => 'Updated notes',
        ]);
    }

    public function test_validation_rules_for_new_fields()
    {
        $parent = User::factory()->create(['user_type' => 'parent']);
        \App\Models\ParentProfile::factory()->create(['id' => $parent->id]);
        
        $response = $this->actingAs($parent)->post(route('parent.children.store'), [
            // Missing required fields
        ]);

        $response->assertSessionHasErrors([
            'relationship',
            'school_start_time',
            'school_end_time',
        ]);
    }

    public function test_validation_rules_for_age_limits()
    {
        $parent = User::factory()->create(['user_type' => 'parent']);
        \App\Models\ParentProfile::factory()->create(['id' => $parent->id]);
        $school = School::factory()->create();
        $location = Location::factory()->create();
        
        // Too young (3 years old)
        $response = $this->actingAs($parent)->post(route('parent.children.store'), [
            'first_name' => 'Baby',
            'last_name' => 'Doe',
            'date_of_birth' => now()->subYears(3)->format('Y-m-d'),
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
            'relationship' => 'Father',
            'school_start_time' => '08:00',
            'school_end_time' => '14:00',
        ]);
        $response->assertSessionHasErrors(['date_of_birth']);

        // Too old (15 years old)
        $response = $this->actingAs($parent)->post(route('parent.children.store'), [
            'first_name' => 'Teen',
            'last_name' => 'Doe',
            'date_of_birth' => now()->subYears(15)->format('Y-m-d'),
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
            'relationship' => 'Father',
            'school_start_time' => '08:00',
            'school_end_time' => '14:00',
        ]);
        $response->assertSessionHasErrors(['date_of_birth']);

        // Correct age (4 years old)
        $response = $this->actingAs($parent)->post(route('parent.children.store'), [
            'first_name' => 'Kid',
            'last_name' => 'Doe',
            'date_of_birth' => now()->subYears(4)->format('Y-m-d'),
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
            'relationship' => 'Father',
            'school_start_time' => '08:00',
            'school_end_time' => '14:00',
        ]);
        $response->assertSessionHasNoErrors();
    }
}
