<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\DriverProfile;
use Illuminate\Support\Facades\Storage;

class AdminDriverViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_pending_drivers_with_details()
    {
        // Create Admin
        $admin = User::factory()->create([
            'user_type' => 'admin',
        ]);

        // Create Pending Driver
        $driver = User::factory()->create([
            'name' => 'Pending Driver',
            'email' => 'pending@driver.com',
            'user_type' => 'driver',
            'status' => 'pending',
        ]);

        // Create Driver Profile
        DriverProfile::create([
            'id' => $driver->id,
            'date_of_birth' => '1990-01-01',
            'license_number' => 'LIC-12345',
            'license_plate' => 'ABC-999',
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Corolla',
            'max_child_capacity' => 4,
            'license_file_path' => 'documents/license.pdf',
            'vehicle_registration_file_path' => 'documents/reg.pdf',
            'gov_id_file_path' => 'documents/id.pdf',
        ]);

        // Act as Admin
        $response = $this->actingAs($admin)->get('/admin/drivers/pending');

        // Assertions
        $response->assertStatus(200);
        $response->assertSee('Pending Driver');
        $response->assertSee('pending@driver.com');
        $response->assertSee('1990-01-01');
        $response->assertSee('ABC-999');
        $response->assertSee('Toyota Corolla');
        $response->assertSee('Capacity: 4 kids');
        $response->assertSee('License #: LIC-12345');
        
        // Check for document links
        // Note: Storage::url('documents/license.pdf') usually returns /storage/documents/license.pdf
        // We check for the relative part or expected URL structure
        $response->assertSee('documents/license.pdf');
        $response->assertSee('documents/reg.pdf');
        $response->assertSee('documents/id.pdf');
    }
}
