<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Service;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@urbanhelp.com',
            'phone' => '+911234567890',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'identity_verified_at' => now(),
            'is_active' => true,
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'country' => 'India',
            'background_check_status' => 'approved'
        ]);

        // Create Provider User
        User::create([
            'name' => 'Service Provider',
            'email' => 'provider@urbanhelp.com',
            'phone' => '+919876543210',
            'password' => Hash::make('password123'),
            'role' => 'provider',
            'provider_type' => 'cleaner',
            'experience_years' => 5,
            'hourly_rate' => 500.00,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'identity_verified_at' => now(),
            'is_active' => true,
            'city' => 'Delhi',
            'state' => 'Delhi',
            'country' => 'India',
            'rating' => 4.5,
            'total_reviews' => 25,
            'total_bookings' => 30,
            'completed_bookings' => 28,
            'availability_status' => 'available',
            'background_check_status' => 'approved'
        ]);

        // Create Customer User
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@urbanhelp.com',
            'phone' => '+915555555555',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true,
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'country' => 'India',
            'background_check_status' => 'not_required'
        ]);

        // Create Categories
        $cleaning = Category::create([
            'name' => 'Home Cleaning',
            'description' => 'Professional home cleaning services'
        ]);

        $plumbing = Category::create([
            'name' => 'Plumbing',
            'description' => 'Plumbing repair and installation services'
        ]);

        $electrical = Category::create([
            'name' => 'Electrical',
            'description' => 'Electrical repair and installation services'
        ]);

        $carpentry = Category::create([
            'name' => 'Carpentry',
            'description' => 'Furniture and woodwork services'
        ]);

        // Create Services
        Service::create([
            'category_id' => $cleaning->id,
            'name' => 'Deep House Cleaning',
            'description' => 'Complete deep cleaning of your home',
            'price' => 1500.00,
            'duration' => 180
        ]);

        Service::create([
            'category_id' => $plumbing->id,
            'name' => 'Pipe Repair',
            'description' => 'Fix leaking or broken pipes',
            'price' => 800.00,
            'duration' => 60
        ]);

        Service::create([
            'category_id' => $electrical->id,
            'name' => 'Electrical Wiring',
            'description' => 'Complete electrical wiring for homes',
            'price' => 2500.00,
            'duration' => 240
        ]);

        Service::create([
            'category_id' => $carpentry->id,
            'name' => 'Furniture Assembly',
            'description' => 'Assembly of furniture and fixtures',
            'price' => 1200.00,
            'duration' => 120
        ]);

        // Seed roles and permissions
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}