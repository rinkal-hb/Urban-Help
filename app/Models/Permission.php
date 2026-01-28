<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
        'action',
        'resource'
    ];

    /**
     * Roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
                    ->withTimestamps();
    }

    /**
     * Find permission by name
     */
    public static function findByName(string $name): ?Permission
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get permissions by module
     */
    public static function getByModule(string $module): Collection
    {
        return static::where('module', $module)->get();
    }

    /**
     * Get full permission name (module.action.resource)
     */
    public function getFullName(): string
    {
        return "{$this->module}.{$this->action}.{$this->resource}";
    }

    /**
     * Validate permission combination
     */
    public static function isValidCombination(string $module, string $action, string $resource): bool
    {
        $validModules = ['users', 'bookings', 'services', 'categories', 'payments', 'roles', 'permissions'];
        $validActions = ['create', 'read', 'update', 'delete', 'manage', 'view'];
        
        if (!in_array($module, $validModules)) {
            return false;
        }
        
        if (!in_array($action, $validActions)) {
            return false;
        }
        
        // Validate resource based on module
        $validResources = match($module) {
            'users' => ['profile', 'account', 'roles', 'all'],
            'bookings' => ['own', 'assigned', 'all'],
            'services' => ['own', 'category', 'all'],
            'categories' => ['all'],
            'payments' => ['own', 'all'],
            'roles' => ['all'],
            'permissions' => ['all'],
            default => ['all']
        };
        
        return in_array($resource, $validResources);
    }

    /**
     * Create permission with validation
     */
    public static function createWithValidation(array $data): ?Permission
    {
        if (!self::isValidCombination($data['module'], $data['action'], $data['resource'])) {
            return null;
        }
        
        $data['name'] = "{$data['module']}.{$data['action']}.{$data['resource']}";
        
        return static::create($data);
    }

    /**
     * Scope for permissions by module
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for permissions by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Check if permission is wildcard (manage action)
     */
    public function isWildcard(): bool
    {
        return $this->action === 'manage';
    }

    /**
     * Get all available modules
     */
    public static function getAvailableModules(): array
    {
        return ['users', 'bookings', 'services', 'categories', 'payments', 'roles', 'permissions'];
    }

    /**
     * Get all available actions
     */
    public static function getAvailableActions(): array
    {
        return ['create', 'read', 'update', 'delete', 'manage', 'view'];
    }
}