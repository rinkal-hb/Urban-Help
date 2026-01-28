<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@urbanhelp.com'
        ], [
            'name' => 'Super Administrator',
            'phone' => '+911111111111',
            'password' => Hash::make('SuperAdmin@123'),
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

        // Assign super_admin role if it exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole && !$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: superadmin@urbanhelp.com');
        $this->command->info('Password: SuperAdmin@123');
    }
}