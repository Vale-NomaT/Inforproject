<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\DriverProfile;

class DriverDocumentUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_see_status_on_dashboard()
    {
        $driver = User::factory()->create([
            'user_type' => 'driver',
            'status' => 'rejected',
            'status_reason' => 'Invalid License',
        ]);
        
        DriverProfile::factory()->create(['id' => $driver->id]);

        $response = $this->actingAs($driver)->get('/driver/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Account Status: Suspended');
        $response->assertSee('Invalid License');
        $response->assertSee('Update Documents');
    }

    public function test_driver_can_view_document_update_page()
    {
        $driver = User::factory()->create([
            'user_type' => 'driver',
            'status' => 'rejected',
        ]);
        
        DriverProfile::factory()->create(['id' => $driver->id]);

        $response = $this->actingAs($driver)->get('/driver/documents');

        $response->assertStatus(200);
        $response->assertSee('Update Documents');
        $response->assertSee("Driver's License", false);
    }

    public function test_driver_can_update_documents_and_reset_status()
    {
        Storage::fake('public');

        $driver = User::factory()->create([
            'user_type' => 'driver',
            'status' => 'rejected',
            'status_reason' => 'Blurry Image',
        ]);
        
        $profile = DriverProfile::factory()->create([
            'id' => $driver->id,
            'license_file_path' => 'old_license.pdf',
        ]);

        $newLicense = UploadedFile::fake()->create('license.pdf', 100);

        $response = $this->actingAs($driver)->post('/driver/documents', [
            'license_file' => $newLicense,
        ]);

        $response->assertRedirect('/driver/dashboard');
        $response->assertSessionHas('success');

        $driver->refresh();
        $profile->refresh();

        $this->assertEquals('pending', $driver->status);
        $this->assertNull($driver->status_reason);
        $this->assertNotEquals('old_license.pdf', $profile->license_file_path);
        
        Storage::disk('public')->assertExists($profile->license_file_path);
    }
}
