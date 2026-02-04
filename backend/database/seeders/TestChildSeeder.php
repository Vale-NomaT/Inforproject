<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestChildSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or Create Parent User
        $parent = \App\Models\User::where('user_type', 'parent')->first();

        if (!$parent) {
            $this->command->info("Creating new Parent User...");
            $parent = \App\Models\User::create([
                'name' => 'Test Parent',
                'email' => 'testparent_child@example.com',
                'password' => bcrypt('password'),
                'user_type' => 'parent',
                'status' => 'active',
            ]);
            
            \App\Models\ParentProfile::create([
                'id' => $parent->id,
                'phone' => '1234567890',
                'relationship_to_child' => 'Parent',
            ]);
        } else {
            $this->command->info("Using existing Parent User: {$parent->name} ({$parent->email})");
        }

        // Find or Create School
        $school = \App\Models\School::first();
        if (!$school) {
            $this->command->info("Creating new School...");
            $school = \App\Models\School::create([
                'name' => 'Greenwood High',
                'city' => 'Springfield',
                'country' => 'USA',
                'lat' => 40.7128,
                'lng' => -74.0060,
                'is_active' => true,
            ]);
        } else {
            $this->command->info("Using existing School: {$school->name}");
        }

        // Find or Create Location
        $location = \App\Models\Location::first();
        if (!$location) {
            $this->command->info("Creating new Location...");
            $location = \App\Models\Location::create([
                'name' => 'Home Base',
                'city' => 'Springfield',
                'country' => 'USA',
                'lat' => 40.7128,
                'lng' => -74.0060,
                'is_active' => true,
            ]);
        } else {
            $this->command->info("Using existing Location: {$location->name}");
        }

        // Create Child
        $this->command->info("Creating Child...");
        
        $child = \App\Models\Child::create([
            'parent_id' => $parent->id,
            'first_name' => 'Junior',
            'last_name' => 'Doe',
            'date_of_birth' => '2015-05-15',
            'school_id' => $school->id,
            'pickup_location_id' => $location->id,
            'relationship' => 'Father',
            'school_start_time' => '08:30',
            'school_end_time' => '15:30',
            'medical_notes' => 'Peanut allergy',
        ]);
        
        $this->command->info("Child Created Successfully!");
        $this->command->info("ID: {$child->id}");
        $this->command->info("Name: {$child->first_name} {$child->last_name}");
        $this->command->info("Relationship: {$child->relationship}");
    }
}
