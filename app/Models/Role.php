<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
        'hierarchy_level'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hierarchy_level' => 'integer'
    ];

    /**
     * Users that have this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
                    ->withPivot('assigned_at', 'assigned_by')
                    ->withTimestamps();
    }

    /**
     * Permissions assigned to this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->withTimestamps();
    }

    /**
     * Give permission to this role
     */
    public function givePermissionTo(string|Permission $permission): void
    {
        $permissionModel = is_string($permission) 
            ? Permission::findByName($permission)
            : $permission;

        if ($permissionModel && !$this->hasPermissionTo($permissionModel)) {
            $this->permissions()->attach($permissionModel->id);
        }
    }

    /**
     * Revoke permission from this role
     */
    public function revokePermissionTo(string|Permission $permission): void
    {
        $permissionModel = is_string($permission) 
            ? Permission::findByName($permission)
            : $permission;

        if ($permissionModel) {
            $this->permissions()->detach($permissionModel->id);
        }
    }

    /**
     * Check if role has specific permission
     */
    public function hasPermissionTo(string|Permission $permission): bool
    {
        $permissionName = is_string($permission) ? $permission : $permission->name;
        
        return $this->permissions()
                    ->where('name', $permissionName)
                    ->exists();
    }

    /**
     * Find role by name
     */
    public static function findByName(string $name): ?Role
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get all permission names for this role
     */
    public function getPermissionNames(): Collection
    {
        return $this->permissions()->pluck('name');
    }

    /**
     * Scope for active roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for roles by hierarchy level
     */
    public function scopeByHierarchy($query, int $level)
    {
        return $query->where('hierarchy_level', '>=', $level);
    }
}