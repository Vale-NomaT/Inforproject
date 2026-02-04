<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DriverProfile;
use App\Models\ParentProfile;
use App\Models\Location;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@safekids.test',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Parent: Sarah Johnson
        $sarah = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'parent',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        ParentProfile::create([
            'id' => $sarah->id,
            'relationship_to_child' => 'Mother',
            'secondary_phone' => '555-0101',
            'address_street' => '123 Main Street',
            'address_city' => 'Bulawayo',
            'address_country' => 'Zimbabwe',
        ]);

        // Driver A: Serves Bulawayo City Hall + Whitestone
        $driverA = User::create([
            'name' => 'Driver A',
            'email' => 'driver.a@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'driver',
            'status' => 'active', // 'approved' maps to 'active'
            'email_verified_at' => now(),
        ]);

        $profileA = DriverProfile::create([
            'id' => $driverA->id,
            'date_of_birth' => '1985-05-15',
            'gov_id_number' => 'AB123456',
            'license_number' => 'L123456789',
            'vehicle_make' => 'Toyota',
            'vehicle_model' => 'Camry',
            'vehicle_year' => '2020',
            'vehicle_color' => 'Silver',
            'license_plate' => 'KIDS-001',
            'max_child_capacity' => 4,
            'vehicle_type' => 'sedan',
        ]);

        // Assign Locations/Schools to Driver A
        $cityHall = Location::where('name', 'Bulawayo City Hall')->first();
        $whitestone = School::where('name', 'Whitestone School')->first();
        if ($cityHall) $profileA->locations()->attach($cityHall->id);
        if ($whitestone) $profileA->schools()->attach($whitestone->id);

        // Driver B: Serves Bradfield + Hillside
        $driverB = User::create([
            'name' => 'Driver B',
            'email' => 'driver.b@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'driver',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $profileB = DriverProfile::create([
            'id' => $driverB->id,
            'date_of_birth' => '1990-08-22',
            'gov_id_number' => 'CD789012',
            'license_number' => 'L987654321',
            'vehicle_make' => 'Honda',
            'vehicle_model' => 'Odyssey',
            'vehicle_year' => '2019',
            'vehicle_color' => 'Blue',
            'license_plate' => 'KIDS-002',
            'max_child_capacity' => 6,
            'vehicle_type' => 'minivan',
        ]);

        $bradfield = Location::where('name', 'Bradfield Shopping Centre')->first();
        $hillside = School::where('name', 'Hillside Primary School')->first();
        if ($bradfield) $profileB->locations()->attach($bradfield->id);
        if ($hillside) $profileB->schools()->attach($hillside->id);

        // Driver C: Pending
        $driverC = User::create([
            'name' => 'Driver C',
            'email' => 'driver.c@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'driver',
            'status' => 'pending',
            'email_verified_at' => now(),
        ]);

        DriverProfile::create([
            'id' => $driverC->id,
            'date_of_birth' => '1992-11-30',
            'gov_id_number' => 'EF345678',
            'license_number' => 'L456123789',
            'vehicle_make' => 'Ford',
            'vehicle_model' => 'Focus',
            'vehicle_year' => '2021',
            'vehicle_color' => 'Red',
            'license_plate' => 'KIDS-003',
            'max_child_capacity' => 4,
            'vehicle_type' => 'sedan',
        ]);
    }
}
