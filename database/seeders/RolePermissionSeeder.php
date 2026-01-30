<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions first
        $this->createPermissions();
        
        // Create roles
        $this->createRoles();
        
        // Assign permissions to roles
        $this->assignPermissionsToRoles();
        
        // Assign roles to existing users
        $this->assignRolesToUsers();
    }

    /**
     * Create comprehensive permissions for Urban Company clone
     */
    protected function createPermissions(): void
    {
        $permissions = [
            // Dashboard permissions
            ['name' => 'dashboard.view.all', 'display_name' => 'View Dashboard', 'description' => 'Access to admin dashboard', 'module' => 'dashboard', 'action' => 'view', 'resource' => 'all'],
            ['name' => 'dashboard.manage.all', 'display_name' => 'Manage Dashboard', 'description' => 'Full dashboard management', 'module' => 'dashboard', 'action' => 'manage', 'resource' => 'all'],

            // User Management
            ['name' => 'users.create.all', 'display_name' => 'Create Users', 'description' => 'Create new users', 'module' => 'users', 'action' => 'create', 'resource' => 'all'],
            ['name' => 'users.read.all', 'display_name' => 'View All Users', 'description' => 'View all users', 'module' => 'users', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'users.read.own', 'display_name' => 'View Own Profile', 'description' => 'View own profile', 'module' => 'users', 'action' => 'read', 'resource' => 'own'],
            ['name' => 'users.update.all', 'display_name' => 'Update All Users', 'description' => 'Update any user', 'module' => 'users', 'action' => 'update', 'resource' => 'all'],
            ['name' => 'users.update.own', 'display_name' => 'Update Own Profile', 'description' => 'Update own profile', 'module' => 'users', 'action' => 'update', 'resource' => 'own'],
            ['name' => 'users.delete.all', 'display_name' => 'Delete Users', 'description' => 'Delete any user', 'module' => 'users', 'action' => 'delete', 'resource' => 'all'],
            ['name' => 'users.manage.all', 'display_name' => 'Manage All Users', 'description' => 'Full user management', 'module' => 'users', 'action' => 'manage', 'resource' => 'all'],

            // Role Management
            ['name' => 'roles.create.all', 'display_name' => 'Create Roles', 'description' => 'Create new roles', 'module' => 'roles', 'action' => 'create', 'resource' => 'all'],
            ['name' => 'roles.read.all', 'display_name' => 'View Roles', 'description' => 'View all roles', 'module' => 'roles', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'roles.update.all', 'display_name' => 'Update Roles', 'description' => 'Update roles', 'module' => 'roles', 'action' => 'update', 'resource' => 'all'],
            ['name' => 'roles.delete.all', 'display_name' => 'Delete Roles', 'description' => 'Delete roles', 'module' => 'roles', 'action' => 'delete', 'resource' => 'all'],
            ['name' => 'roles.manage.all', 'display_name' => 'Manage Roles', 'description' => 'Full role management', 'module' => 'roles', 'action' => 'manage', 'resource' => 'all'],

            // Permission Management
            ['name' => 'permissions.read.all', 'display_name' => 'View Permissions', 'description' => 'View all permissions', 'module' => 'permissions', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'permissions.manage.all', 'display_name' => 'Manage Permissions', 'description' => 'Full permission management', 'module' => 'permissions', 'action' => 'manage', 'resource' => 'all'],

            // Category Management
            ['name' => 'categories.create.all', 'display_name' => 'Create Categories', 'description' => 'Create new categories', 'module' => 'categories', 'action' => 'create', 'resource' => 'all'],
            ['name' => 'categories.read.all', 'display_name' => 'View Categories', 'description' => 'View all categories', 'module' => 'categories', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'categories.update.all', 'display_name' => 'Update Categories', 'description' => 'Update categories', 'module' => 'categories', 'action' => 'update', 'resource' => 'all'],
            ['name' => 'categories.delete.all', 'display_name' => 'Delete Categories', 'description' => 'Delete categories', 'module' => 'categories', 'action' => 'delete', 'resource' => 'all'],
            ['name' => 'categories.manage.all', 'display_name' => 'Manage Categories', 'description' => 'Full category management', 'module' => 'categories', 'action' => 'manage', 'resource' => 'all'],

            // Service Management
            ['name' => 'services.create.all', 'display_name' => 'Create All Services', 'description' => 'Create services for any provider', 'module' => 'services', 'action' => 'create', 'resource' => 'all'],
            ['name' => 'services.create.own', 'display_name' => 'Create Own Services', 'description' => 'Create own services', 'module' => 'services', 'action' => 'create', 'resource' => 'own'],
            ['name' => 'services.read.all', 'display_name' => 'View All Services', 'description' => 'View all services', 'module' => 'services', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'services.read.own', 'display_name' => 'View Own Services', 'description' => 'View own services', 'module' => 'services', 'action' => 'read', 'resource' => 'own'],
            ['name' => 'services.update.all', 'display_name' => 'Update All Services', 'description' => 'Update any service', 'module' => 'services', 'action' => 'update', 'resource' => 'all'],
            ['name' => 'services.update.own', 'display_name' => 'Update Own Services', 'description' => 'Update own services', 'module' => 'services', 'action' => 'update', 'resource' => 'own'],
            ['name' => 'services.delete.all', 'display_name' => 'Delete All Services', 'description' => 'Delete any service', 'module' => 'services', 'action' => 'delete', 'resource' => 'all'],
            ['name' => 'services.delete.own', 'display_name' => 'Delete Own Services', 'description' => 'Delete own services', 'module' => 'services', 'action' => 'delete', 'resource' => 'own'],
            ['name' => 'services.manage.all', 'display_name' => 'Manage All Services', 'description' => 'Full service management', 'module' => 'services', 'action' => 'manage', 'resource' => 'all'],
            ['name' => 'services.manage.own', 'display_name' => 'Manage Own Services', 'description' => 'Manage own services', 'module' => 'services', 'action' => 'manage', 'resource' => 'own'],

            // Booking Management
            ['name' => 'bookings.create.all', 'display_name' => 'Create All Bookings', 'description' => 'Create bookings for any user', 'module' => 'bookings', 'action' => 'create', 'resource' => 'all'],
            ['name' => 'bookings.create.own', 'display_name' => 'Create Own Bookings', 'description' => 'Create own bookings', 'module' => 'bookings', 'action' => 'create', 'resource' => 'own'],
            ['name' => 'bookings.read.all', 'display_name' => 'View All Bookings', 'description' => 'View all bookings', 'module' => 'bookings', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'bookings.read.own', 'display_name' => 'View Own Bookings', 'description' => 'View own bookings', 'module' => 'bookings', 'action' => 'read', 'resource' => 'own'],
            ['name' => 'bookings.read.assigned', 'display_name' => 'View Assigned Bookings', 'description' => 'View assigned bookings', 'module' => 'bookings', 'action' => 'read', 'resource' => 'assigned'],
            ['name' => 'bookings.update.all', 'display_name' => 'Update All Bookings', 'description' => 'Update any booking', 'module' => 'bookings', 'action' => 'update', 'resource' => 'all'],
            ['name' => 'bookings.update.own', 'display_name' => 'Update Own Bookings', 'description' => 'Update own bookings', 'module' => 'bookings', 'action' => 'update', 'resource' => 'own'],
            ['name' => 'bookings.update.assigned', 'display_name' => 'Update Assigned Bookings', 'description' => 'Update assigned bookings', 'module' => 'bookings', 'action' => 'update', 'resource' => 'assigned'],
            ['name' => 'bookings.delete.all', 'display_name' => 'Delete All Bookings', 'description' => 'Delete any booking', 'module' => 'bookings', 'action' => 'delete', 'resource' => 'all'],
            ['name' => 'bookings.manage.all', 'display_name' => 'Manage All Bookings', 'description' => 'Full booking management', 'module' => 'bookings', 'action' => 'manage', 'resource' => 'all'],

            // Payment Management
            ['name' => 'payments.read.all', 'display_name' => 'View All Payments', 'description' => 'View all payments', 'module' => 'payments', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'payments.read.own', 'display_name' => 'View Own Payments', 'description' => 'View own payments', 'module' => 'payments', 'action' => 'read', 'resource' => 'own'],
            ['name' => 'payments.update.all', 'display_name' => 'Update All Payments', 'description' => 'Update any payment', 'module' => 'payments', 'action' => 'update', 'resource' => 'all'],
            ['name' => 'payments.manage.all', 'display_name' => 'Manage All Payments', 'description' => 'Full payment management', 'module' => 'payments', 'action' => 'manage', 'resource' => 'all'],

            // Provider Assignment
            ['name' => 'providers.assign.all', 'display_name' => 'Assign Providers', 'description' => 'Assign providers to bookings', 'module' => 'providers', 'action' => 'assign', 'resource' => 'all'],
            ['name' => 'providers.read.all', 'display_name' => 'View All Providers', 'description' => 'View all providers', 'module' => 'providers', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'providers.manage.all', 'display_name' => 'Manage Providers', 'description' => 'Full provider management', 'module' => 'providers', 'action' => 'manage', 'resource' => 'all'],

            // Customer Management
            ['name' => 'customers.read.all', 'display_name' => 'View All Customers', 'description' => 'View all customers', 'module' => 'customers', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'customers.manage.all', 'display_name' => 'Manage Customers', 'description' => 'Full customer management', 'module' => 'customers', 'action' => 'manage', 'resource' => 'all'],

            // Reports & Analytics
            ['name' => 'reports.read.all', 'display_name' => 'View Reports', 'description' => 'View all reports', 'module' => 'reports', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'reports.export.all', 'display_name' => 'Export Reports', 'description' => 'Export reports', 'module' => 'reports', 'action' => 'export', 'resource' => 'all'],

            // System Settings
            ['name' => 'settings.read.all', 'display_name' => 'View Settings', 'description' => 'View system settings', 'module' => 'settings', 'action' => 'read', 'resource' => 'all'],
            ['name' => 'settings.update.all', 'display_name' => 'Update Settings', 'description' => 'Update system settings', 'module' => 'settings', 'action' => 'update', 'resource' => 'all'],

            // Audit Logs
            ['name' => 'audit.read.all', 'display_name' => 'View Audit Logs', 'description' => 'View audit logs', 'module' => 'audit', 'action' => 'read', 'resource' => 'all'],

            // Super Admin Permission
            ['name' => '*.manage.all', 'display_name' => 'Super Admin Access', 'description' => 'Full access to all system features', 'module' => '*', 'action' => 'manage', 'resource' => 'all'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }

    /**
     * Create default roles with proper hierarchy
     */
    protected function createRoles(): void
    {
        // Super Admin Role
        Role::firstOrCreate([
            'name' => 'super_admin'
        ], [
            'display_name' => 'Super Administrator',
            'description' => 'Full system access with all permissions',
            'is_active' => true,
            'hierarchy_level' => 100
        ]);

        // Admin Role
        Role::firstOrCreate([
            'name' => 'admin'
        ], [
            'display_name' => 'Administrator',
            'description' => 'Administrative access to most system features',
            'is_active' => true,
            'hierarchy_level' => 90
        ]);

        // Manager Role
        Role::firstOrCreate([
            'name' => 'manager'
        ], [
            'display_name' => 'Manager',
            'description' => 'Manage bookings, providers, and customers',
            'is_active' => true,
            'hierarchy_level' => 70
        ]);

        // Service Provider Role
        Role::firstOrCreate([
            'name' => 'provider'
        ], [
            'display_name' => 'Service Provider',
            'description' => 'Manage own services and bookings',
            'is_active' => true,
            'hierarchy_level' => 50
        ]);

        // Customer Role
        Role::firstOrCreate([
            'name' => 'customer'
        ], [
            'display_name' => 'Customer',
            'description' => 'Book services and manage own profile',
            'is_active' => true,
            'hierarchy_level' => 10
        ]);
    }

    /**
     * Assign permissions to roles based on hierarchy
     */
    protected function assignPermissionsToRoles(): void
    {
        // Super Admin - All permissions
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync(Permission::all()->pluck('id'));
        }

        // Admin - Most permissions except super admin functions
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $adminPermissions = Permission::whereNotIn('name', [
                '*.manage.all',
                'roles.delete.all',
                'permissions.manage.all'
            ])->pluck('id');
            $admin->permissions()->sync($adminPermissions);
        }

        // Manager - Booking and service management
        $manager = Role::where('name', 'manager')->first();
        if ($manager) {
            $managerPermissions = Permission::whereIn('name', [
                'dashboard.view.all',
                'bookings.manage.all',
                'services.read.all',
                'services.update.all',
                'providers.read.all',
                'providers.assign.all',
                'customers.read.all',
                'payments.read.all',
                'reports.read.all',
                'users.read.all',
                'users.update.own'
            ])->pluck('id');
            $manager->permissions()->sync($managerPermissions);
        }

        // Provider - Own services and assigned bookings
        $provider = Role::where('name', 'provider')->first();
        if ($provider) {
            $providerPermissions = Permission::whereIn('name', [
                'services.manage.own',
                'bookings.read.assigned',
                'bookings.update.assigned',
                'payments.read.own',
                'users.read.own',
                'users.update.own'
            ])->pluck('id');
            $provider->permissions()->sync($providerPermissions);
        }

        // Customer - Basic booking and profile management
        $customer = Role::where('name', 'customer')->first();
        if ($customer) {
            $customerPermissions = Permission::whereIn('name', [
                'bookings.create.own',
                'bookings.read.own',
                'bookings.update.own',
                'services.read.all',
                'categories.read.all',
                'payments.read.own',
                'users.read.own',
                'users.update.own'
            ])->pluck('id');
            $customer->permissions()->sync($customerPermissions);
        }
    }

    /**
     * Assign roles to existing users based on their role field
     */
    protected function assignRolesToUsers(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Skip if user already has roles
            if ($user->roles()->exists()) {
                continue;
            }

            // Assign role based on legacy role field
            $roleName = match($user->role) {
                'admin' => 'admin',
                'super_admin' => 'super_admin',
                'provider' => 'provider',
                'customer' => 'customer',
                default => 'customer'
            };

            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->attach($role->id, [
                    'assigned_at' => now(),
                    'assigned_by' => null
                ]);
            }
        }
    }
}