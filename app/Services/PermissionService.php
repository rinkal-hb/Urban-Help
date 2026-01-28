<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Check if user has specific permission
     */
    public function checkPermission(User $user, string $permission): bool
    {
        if (!$user->is_active || $user->isLocked()) {
            return false;
        }

        $cacheKey = "user_permissions_{$user->id}";
        
        $permissions = Cache::remember($cacheKey, 3600, function () use ($user) {
            return $this->getUserPermissions($user);
        });
        
        // Check direct permission
        if ($permissions->contains('name', $permission)) {
            return true;
        }
        
        // Check wildcard permissions
        $permissionParts = explode('.', $permission);
        if (count($permissionParts) === 3) {
            $wildcardPermission = $permissionParts[0] . '.manage.' . $permissionParts[2];
            if ($permissions->contains('name', $wildcardPermission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get all permissions for a user
     */
    public function getUserPermissions(User $user): Collection
    {
        return $user->roles()
                   ->with('permissions')
                   ->get()
                   ->pluck('permissions')
                   ->flatten()
                   ->unique('id');
    }

    /**
     * Assign role to user
     */
    public function assignRoleToUser(User $user, string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::findByName($role) : $role;
        
        if (!$roleModel) {
            throw new \InvalidArgumentException("Role not found");
        }
        
        if (!$user->hasRole($roleModel->name)) {
            $user->assignRole($roleModel);
            $this->clearUserPermissionCache($user);
        }
    }

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(User $user, string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::findByName($role) : $role;
        
        if (!$roleModel) {
            throw new \InvalidArgumentException("Role not found");
        }
        
        $user->removeRole($roleModel);
        $this->clearUserPermissionCache($user);
    }

    /**
     * Create new role
     */
    public function createRole(array $data): Role
    {
        DB::beginTransaction();
        
        try {
            $role = Role::create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'hierarchy_level' => $data['hierarchy_level'] ?? 0
            ]);
            
            if (isset($data['permissions'])) {
                $this->syncRolePermissions($role, $data['permissions']);
            }
            
            DB::commit();
            
            // Log the role creation
            if (auth()->check()) {
                auth()->user()->logActivity('role_created', [
                    'role_name' => $role->name,
                    'role_id' => $role->id
                ]);
            }
            
            return $role;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update existing role
     */
    public function updateRole(Role $role, array $data): Role
    {
        DB::beginTransaction();
        
        try {
            $oldData = $role->toArray();
            
            $role->update([
                'display_name' => $data['display_name'] ?? $role->display_name,
                'description' => $data['description'] ?? $role->description,
                'is_active' => $data['is_active'] ?? $role->is_active,
                'hierarchy_level' => $data['hierarchy_level'] ?? $role->hierarchy_level
            ]);
            
            if (isset($data['permissions'])) {
                $this->syncRolePermissions($role, $data['permissions']);
            }
            
            DB::commit();
            
            // Clear cache for all users with this role
            $this->clearRoleUsersCache($role);
            
            // Log the role update
            if (auth()->check()) {
                auth()->user()->logActivity('role_updated', [
                    'role_name' => $role->name,
                    'role_id' => $role->id,
                    'old_data' => $oldData,
                    'new_data' => $role->fresh()->toArray()
                ]);
            }
            
            return $role;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete role (only if no users assigned)
     */
    public function deleteRole(Role $role): bool
    {
        if ($role->users()->count() > 0) {
            throw new \InvalidArgumentException("Cannot delete role with assigned users");
        }
        
        DB::beginTransaction();
        
        try {
            $roleName = $role->name;
            $roleId = $role->id;
            
            // Remove all permissions
            $role->permissions()->detach();
            
            // Delete the role
            $role->delete();
            
            DB::commit();
            
            // Log the role deletion
            if (auth()->check()) {
                auth()->user()->logActivity('role_deleted', [
                    'role_name' => $roleName,
                    'role_id' => $roleId
                ]);
            }
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Sync permissions for a role
     */
    public function syncRolePermissions(Role $role, array $permissions): void
    {
        $permissionIds = collect($permissions)->map(function ($permission) {
            if (is_string($permission)) {
                return Permission::findByName($permission)?->id;
            } elseif (is_array($permission) && isset($permission['id'])) {
                return $permission['id'];
            } elseif ($permission instanceof Permission) {
                return $permission->id;
            }
            return null;
        })->filter()->toArray();
        
        $role->permissions()->sync($permissionIds);
        
        // Clear cache for all users with this role
        $this->clearRoleUsersCache($role);
        
        // Log permission sync
        if (auth()->check()) {
            auth()->user()->logActivity('role_permissions_synced', [
                'role_name' => $role->name,
                'role_id' => $role->id,
                'permission_ids' => $permissionIds
            ]);
        }
    }

    /**
     * Clear permission cache for a user
     */
    public function clearUserPermissionCache(User $user): void
    {
        Cache::forget("user_permissions_{$user->id}");
    }

    /**
     * Cache user permissions
     */
    public function cacheUserPermissions(User $user): void
    {
        $cacheKey = "user_permissions_{$user->id}";
        $permissions = $this->getUserPermissions($user);
        Cache::put($cacheKey, $permissions, 3600);
    }

    /**
     * Clear cache for all users with a specific role
     */
    protected function clearRoleUsersCache(Role $role): void
    {
        $userIds = $role->users()->pluck('id');
        
        foreach ($userIds as $userId) {
            Cache::forget("user_permissions_{$userId}");
        }
    }

    /**
     * Get role hierarchy with inherited permissions
     */
    public function getRoleHierarchy(Role $role): Collection
    {
        $lowerRoles = Role::where('hierarchy_level', '<', $role->hierarchy_level)
                         ->where('is_active', true)
                         ->with('permissions')
                         ->get();
        
        $inheritedPermissions = $lowerRoles->pluck('permissions')
                                          ->flatten()
                                          ->unique('id');
        
        $directPermissions = $role->permissions;
        
        return $inheritedPermissions->merge($directPermissions)->unique('id');
    }

    /**
     * Check if user can assign/remove roles
     */
    public function canManageRoles(User $user): bool
    {
        return $this->checkPermission($user, 'roles.manage.all') ||
               $this->checkPermission($user, 'users.manage.roles');
    }

    /**
     * Get available permissions grouped by module
     */
    public function getPermissionsByModule(): Collection
    {
        return Permission::all()->groupBy('module');
    }

    /**
     * Create default permissions for a module
     */
    public function createDefaultPermissions(string $module): Collection
    {
        $actions = ['create', 'read', 'update', 'delete', 'manage'];
        $resources = ['own', 'all'];
        $permissions = collect();
        
        foreach ($actions as $action) {
            foreach ($resources as $resource) {
                if (Permission::isValidCombination($module, $action, $resource)) {
                    $permission = Permission::createWithValidation([
                        'module' => $module,
                        'action' => $action,
                        'resource' => $resource,
                        'display_name' => ucfirst($action) . ' ' . ucfirst($resource) . ' ' . ucfirst($module),
                        'description' => "Allow {$action} access to {$resource} {$module}"
                    ]);
                    
                    if ($permission) {
                        $permissions->push($permission);
                    }
                }
            }
        }
        
        return $permissions;
    }
}