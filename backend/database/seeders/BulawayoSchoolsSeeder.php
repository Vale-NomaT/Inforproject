<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\School;

class BulawayoSchoolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation (Database Agnostic)
        Schema::disableForeignKeyConstraints();
        School::truncate();
        DB::table('driver_schools')->truncate();
        Schema::enableForeignKeyConstraints();

        $schools = [
            // Primary Schools
            [
                'name' => 'Whitestone School',
                'address' => 'Whitestone Way, Burnside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.210850,
                'lng' => 28.617400,
                'is_active' => true,
            ],
            [
                'name' => 'Petra Primary School',
                'address' => 'Ilanda',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.185000,
                'lng' => 28.615000,
                'is_active' => true,
            ],
            [
                'name' => 'Carmel School',
                'address' => 'Redrup Street, Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.175000,
                'lng' => 28.605000,
                'is_active' => true,
            ],
            [
                'name' => 'Coghlan Primary School',
                'address' => 'Corner 12th Avenue & Pauling Road, Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.162000,
                'lng' => 28.592000,
                'is_active' => true,
            ],
            [
                'name' => 'Kumalo Primary School',
                'address' => 'Corner George Avenue & 3rd Street, Kumalo',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.145000,
                'lng' => 28.600000,
                'is_active' => true,
            ],
            [
                'name' => 'Centenary School',
                'address' => 'Lawley Road, Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.170000,
                'lng' => 28.585000,
                'is_active' => true,
            ],
            [
                'name' => 'Fairbridge Primary School',
                'address' => 'Queens Road, North End',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.130000,
                'lng' => 28.590000,
                'is_active' => true,
            ],
            [
                'name' => 'Henry Low Primary School',
                'address' => 'Cecil Avenue, Hillside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.198000,
                'lng' => 28.612000,
                'is_active' => true,
            ],
            [
                'name' => 'Hillside Primary School',
                'address' => 'Cecil Avenue, Hillside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.192000,
                'lng' => 28.605000,
                'is_active' => true,
            ],
            [
                'name' => 'Hugh Beadle Primary School',
                'address' => 'Sauerstown',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.115000,
                'lng' => 28.575000,
                'is_active' => true,
            ],
            [
                'name' => 'King George VI Centre',
                'address' => 'George Avenue, Kumalo',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.165000,
                'lng' => 28.595000,
                'is_active' => true,
            ],
            [
                'name' => 'Milton Junior School',
                'address' => 'Townsend Road, Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.160000,
                'lng' => 28.598000,
                'is_active' => true,
            ],
            [
                'name' => 'St. Thomas Aquinas Primary School',
                'address' => 'Corner Park Road & 3rd Avenue, Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.163000,
                'lng' => 28.602000,
                'is_active' => true,
            ],
            [
                'name' => 'Dominican Convent Primary School',
                'address' => 'Lobengula Street, Bulawayo CBD',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.156000,
                'lng' => 28.582000,
                'is_active' => true,
            ],
            [
                'name' => 'Masiyephambili Junior School',
                'address' => 'Montrose',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.178000,
                'lng' => 28.595000,
                'is_active' => true,
            ],
            [
                'name' => 'Baines Junior School',
                'address' => 'North End',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.125000,
                'lng' => 28.580000,
                'is_active' => true,
            ],
            [
                'name' => 'Robert Tredgold Primary School',
                'address' => 'Lobengula Street',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.152000,
                'lng' => 28.588000,
                'is_active' => true,
            ],
            [
                'name' => 'McKeurtan Primary School',
                'address' => 'Kenilworth',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.135000,
                'lng' => 28.585000,
                'is_active' => true,
            ],
            [
                'name' => 'Tennyson Primary School',
                'address' => 'Malindela',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.182000,
                'lng' => 28.588000,
                'is_active' => true,
            ],
            [
                'name' => 'Greenfield Primary School',
                'address' => 'Bellevue',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.188000,
                'lng' => 28.560000,
                'is_active' => true,
            ],

            // Pre-Schools & Creches
            [
                'name' => 'Toddlers Rock Nursery School',
                'address' => 'Burnside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.208000,
                'lng' => 28.615000,
                'is_active' => true,
            ],
            [
                'name' => 'Bambini Pre-School',
                'address' => 'Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.165000,
                'lng' => 28.590000,
                'is_active' => true,
            ],
            [
                'name' => 'Little Steps Pre-School',
                'address' => 'Hillside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.190000,
                'lng' => 28.602000,
                'is_active' => true,
            ],
            [
                'name' => 'Peter Pan Nursery School',
                'address' => 'Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.161000,
                'lng' => 28.591000,
                'is_active' => true,
            ],
            [
                'name' => 'Humpty Dumpty Nursery School',
                'address' => 'Kumalo',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.158000,
                'lng' => 28.605000,
                'is_active' => true,
            ],
            // Additional Primary Schools
            [
                'name' => 'Riverside Primary School',
                'address' => 'Riverside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.205500,
                'lng' => 28.625400,
                'is_active' => true,
            ],
            [
                'name' => 'Matsheumhlope Primary School',
                'address' => 'Matsheumhlope',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.195200,
                'lng' => 28.635100,
                'is_active' => true,
            ],
            [
                'name' => 'Barham Green Primary School',
                'address' => 'Barham Green',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.175500,
                'lng' => 28.565200,
                'is_active' => true,
            ],
            [
                'name' => 'Thomas Rudland Primary School',
                'address' => 'Morningside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.178200,
                'lng' => 28.575500,
                'is_active' => true,
            ],
            [
                'name' => 'Moray Primary School',
                'address' => 'Famona',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.172500,
                'lng' => 28.578800,
                'is_active' => true,
            ],
            [
                'name' => 'Newton West Junior School',
                'address' => 'Newton West',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.185500,
                'lng' => 28.555500,
                'is_active' => true,
            ],
            // Additional Creches
            [
                'name' => 'Jungle Gym Nursery School',
                'address' => 'Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.163500,
                'lng' => 28.595500,
                'is_active' => true,
            ],
            [
                'name' => 'Busy Bees Nursery School',
                'address' => 'Hillside',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.193500,
                'lng' => 28.608500,
                'is_active' => true,
            ],
            [
                'name' => 'Little People Nursery School',
                'address' => 'Malindela',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.183500,
                'lng' => 28.590500,
                'is_active' => true,
            ],
            [
                'name' => 'Girls College Pre-School',
                'address' => 'Suburbs',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.168500,
                'lng' => 28.598500,
                'is_active' => true,
            ],
            [
                'name' => 'Dominican Convent ECD',
                'address' => 'Lobengula Street',
                'city' => 'Bulawayo',
                'country' => 'Zimbabwe',
                'lat' => -20.156500,
                'lng' => 28.582500,
                'is_active' => true,
            ],
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}
