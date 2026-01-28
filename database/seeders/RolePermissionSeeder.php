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
     * Create default permissions
     */
    protected function createPermissions(): void
    {
        $modules = ['users', 'bookings', 'services', 'categories', 'payments', 'roles', 'permissions'];
        $actions = ['create', 'read', 'update', 'delete', 'manage', 'view'];
        $resources = ['own', 'all'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                foreach ($resources as $resource) {
                    // Skip invalid combinations
                    if (!$this->isValidPermissionCombination($module, $action, $resource)) {
                        continue;
                    }

                    Permission::firstOrCreate([
                        'name' => "{$module}.{$action}.{$resource}"
                    ], [
                        'display_name' => $this->getPermissionDisplayName($module, $action, $resource),
                        'description' => $this->getPermissionDescription($module, $action, $resource),
                        'module' => $module,
                        'action' => $action,
                        'resource' => $resource
                    ]);
                }
            }
        }

        // Create special admin permissions
        Permission::firstOrCreate([
            'name' => '*.manage.all'
        ], [
            'display_name' => 'Super Admin Access',
            'description' => 'Full access to all system features',
            'module' => '*',
            'action' => 'manage',
            'resource' => 'all'
        ]);
    }

    /**
     * Create default roles
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

        // Service Provider Role
        Role::firstOrCreate([
            'name' => 'provider'
        ], [
            'display_name' => 'Service Provider',
            'description' => 'Can manage own services and bookings',
            'is_active' => true,
            'hierarchy_level' => 50
        ]);

        // Customer Role
        Role::firstOrCreate([
            'name' => 'customer'
        ], [
            'display_name' => 'Customer',
            'description' => 'Can book services and manage own profile',
            'is_active' => true,
            'hierarchy_level' => 10
        ]);

        // Manager Role
        Role::firstOrCreate([
            'name' => 'manager'
        ], [
            'display_name' => 'Manager',
            'description' => 'Can manage bookings and services',
            'is_active' => true,
            'hierarchy_level' => 70
        ]);
    }

    /**
     * Assign permissions to roles
     */
    protected function assignPermissionsToRoles(): void
    {
        // Super Admin - All permissions
        $superAdmin = Role::where('name', 'super_admin')->first();
        $allPermissions = Permission::all();
        $superAdmin->permissions()->sync($allPermissions->pluck('id'));

        // Admin - Most permissions except super admin ones
        $admin = Role::where('name', 'admin')->first();
        $adminPermissions = Permission::where('name', '!=', '*.manage.all')
                                    ->whereNotIn('name', [
                                        'roles.delete.all',
                                        'permissions.delete.all'
                                    ])
                                    ->get();
        $admin->permissions()->sync($adminPermissions->pluck('id'));

        // Manager - Booking and service management
        $manager = Role::where('name', 'manager')->first();
        $managerPermissions = Permission::whereIn('name', [
            'bookings.manage.all',
            'services.manage.all',
            'categories.read.all',
            'users.read.all',
            'payments.read.all'
        ])->get();
        $manager->permissions()->sync($managerPermissions->pluck('id'));

        // Provider - Own services and bookings
        $provider = Role::where('name', 'provider')->first();
        $providerPermissions = Permission::whereIn('name', [
            'services.manage.own',
            'bookings.read.own',
            'bookings.update.own',
            'categories.read.all',
            'users.update.own',
            'payments.read.own'
        ])->get();
        $provider->permissions()->sync($providerPermissions->pluck('id'));

        // Customer - Basic permissions
        $customer = Role::where('name', 'customer')->first();
        $customerPermissions = Permission::whereIn('name', [
            'bookings.create.own',
            'bookings.read.own',
            'bookings.update.own',
            'services.read.all',
            'categories.read.all',
            'users.update.own',
            'payments.read.own'
        ])->get();
        $customer->permissions()->sync($customerPermissions->pluck('id'));
    }

    /**
     * Assign roles to existing users based on their legacy role field
     */
    protected function assignRolesToUsers(): void
    {
        $users = User::whereNotNull('role')->get();

        foreach ($users as $user) {
            $roleName = match($user->role) {
                'admin' => 'admin',
                'provider' => 'provider',
                'customer' => 'customer',
                default => 'customer'
            };

            $role = Role::where('name', $roleName)->first();
            if ($role && !$user->hasRole($roleName)) {
                $user->assignRole($role);
            }
        }
    }

    /**
     * Check if permission combination is valid
     */
    protected function isValidPermissionCombination(string $module, string $action, string $resource): bool
    {
        // Skip view action for own resource (use read instead)
        if ($action === 'view' && $resource === 'own') {
            return false;
        }

        // Skip manage action for own resource in some modules
        if ($action === 'manage' && $resource === 'own' && in_array($module, ['roles', 'permissions'])) {
            return false;
        }

        // Only allow 'all' resource for roles and permissions
        if (in_array($module, ['roles', 'permissions']) && $resource === 'own') {
            return false;
        }

        return true;
    }

    /**
     * Get permission display name
     */
    protected function getPermissionDisplayName(string $module, string $action, string $resource): string
    {
        $moduleTitle = ucfirst($module);
        $actionTitle = ucfirst($action);
        $resourceTitle = ucfirst($resource);

        return "{$actionTitle} {$resourceTitle} {$moduleTitle}";
    }

    /**
     * Get permission description
     */
    protected function getPermissionDescription(string $module, string $action, string $resource): string
    {
        $actionDesc = match($action) {
            'create' => 'Create new',
            'read' => 'View and read',
            'update' => 'Edit and update',
            'delete' => 'Delete',
            'manage' => 'Full management of',
            'view' => 'View',
            default => $action
        };

        $resourceDesc = match($resource) {
            'own' => 'own',
            'all' => 'all',
            default => $resource
        };

        return "{$actionDesc} {$resourceDesc} {$module}";
    }
}