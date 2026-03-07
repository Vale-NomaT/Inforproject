<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\DriverProfile;

class DriverRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register/driver');

        $response->assertStatus(200);
    }

    public function test_new_drivers_can_register_with_valid_documents()
    {
        Storage::fake('public');

        $licenseFile = UploadedFile::fake()->create('license.pdf', 1000, 'application/pdf');
        $regFile = UploadedFile::fake()->image('registration.jpg');
        $idFile = UploadedFile::fake()->image('id_card.png');

        $response = $this->post('/register/driver', [
            'name' => 'Test Driver',
            'email' => 'driver@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'date_of_birth' => '1990-01-01',
            'license_number' => 'LIC123456',
            'license_plate' => 'ABC-123',
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Corolla',
            'max_child_capacity' => 4,
            'license_document' => $licenseFile,
            'vehicle_registration_document' => $regFile,
            'gov_id_document' => $idFile,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('driver.dashboard'));

        $user = User::where('email', 'driver@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('driver', $user->user_type);
        $this->assertEquals('pending', $user->status);

        $driverProfile = DriverProfile::find($user->id);
        $this->assertNotNull($driverProfile);
        $this->assertEquals('LIC123456', $driverProfile->license_number);

        // Check if files are stored
        Storage::disk('public')->assertExists($driverProfile->license_file_path);
        Storage::disk('public')->assertExists($driverProfile->vehicle_registration_file_path);
        Storage::disk('public')->assertExists($driverProfile->gov_id_file_path);
    }

    public function test_driver_registration_fails_without_documents()
    {
        $response = $this->post('/register/driver', [
            'name' => 'Test Driver',
            'email' => 'driver@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'date_of_birth' => '1990-01-01',
            'license_number' => 'LIC123456',
            'license_plate' => 'ABC-123',
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Corolla',
            'max_child_capacity' => 4,
        ]);

        $response->assertSessionHasErrors(['license_document', 'vehicle_registration_document', 'gov_id_document']);
    }

    public function test_driver_registration_fails_with_invalid_file_types()
    {
        Storage::fake('public');

        $textFile = UploadedFile::fake()->create('document.txt', 1000);

        $response = $this->post('/register/driver', [
            'name' => 'Test Driver',
            'email' => 'driver@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'date_of_birth' => '1990-01-01',
            'license_number' => 'LIC123456',
            'license_plate' => 'ABC-123',
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Corolla',
            'max_child_capacity' => 4,
            'license_document' => $textFile,
            'vehicle_registration_document' => $textFile,
            'gov_id_document' => $textFile,
        ]);

        $response->assertSessionHasErrors(['license_document', 'vehicle_registration_document', 'gov_id_document']);
    }
}
