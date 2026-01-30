<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\AuditLog;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const ROLE_ADMIN = 'admin';
    const ROLE_CUSTOMER = 'customer';
    const ROLE_PROVIDER = 'provider';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'otp',
        'otp_expires_at',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
        'is_active',
        'preferences',
        'avatar',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'provider_type',
        'experience_years',
        'hourly_rate',
        'availability_status',
        'rating',
        'total_reviews',
        'total_bookings',
        'completed_bookings',
        'phone_verified_at',
        'identity_verified_at',
        'background_check_status',
        'documents',
        'notification_preferences',
        'google_id',
        'facebook_id',
        'business_name',
        'business_license',
        'tax_id'
    ];

    protected $hidden = ['password', 'remember_token', 'otp'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'is_active' => 'boolean',
        'status' => 'boolean',
        'preferences' => 'array',
        'failed_login_attempts' => 'integer',
        'date_of_birth' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'experience_years' => 'integer',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'total_bookings' => 'integer',
        'completed_bookings' => 'integer',
        'phone_verified_at' => 'datetime',
        'identity_verified_at' => 'datetime',
        'documents' => 'array',
        'notification_preferences' => 'array'
    ];

    // Role and Permission Methods
    
    /**
     * Roles assigned to this user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot('assigned_at', 'assigned_by')
                    ->withTimestamps();
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string|Role $role): bool
    {
        $roleName = is_string($role) ? $role : $role->name;
        
        return $this->roles()
                    ->where('name', $roleName)
                    ->where('is_active', true)
                    ->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Assign role to user
     */
    public function assignRole(string|Role $role, ?User $assignedBy = null): void
    {
        $roleModel = is_string($role) ? Role::findByName($role) : $role;
        
        if (!$roleModel) {
            throw new \InvalidArgumentException("Role not found");
        }

        if (!$this->hasRole($roleModel->name)) {
            $this->roles()->attach($roleModel->id, [
                'assigned_at' => now(),
                'assigned_by' => $assignedBy?->id
            ]);

            // Clear permission cache
            $this->clearPermissionCache();

            // Log the activity
            $this->logActivity('role_assigned', [
                'role_name' => $roleModel->name,
                'assigned_by' => $assignedBy?->id
            ]);
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::findByName($role) : $role;
        
        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
            
            // Clear permission cache
            $this->clearPermissionCache();

            // Log the activity
            $this->logActivity('role_removed', [
                'role_name' => $roleModel->name
            ]);
        }
    }

    /**
     * Get all permissions for this user through roles
     */
    public function getAllPermissions(): Collection
    {
        $cacheKey = "user_permissions_{$this->id}";
        
        return Cache::remember($cacheKey, 3600, function () {
            return $this->roles()
                       ->with('permissions')
                       ->get()
                       ->pluck('permissions')
                       ->flatten()
                       ->unique('id');
        });
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Check if user is active and not locked
        if (!$this->is_active || $this->isLocked()) {
            return false;
        }

        $permissions = $this->getAllPermissions();
        
        // Direct permission check
        if ($permissions->contains('name', $permission)) {
            return true;
        }
        
        // Check for wildcard permissions
        $permissionParts = explode('.', $permission);
        if (count($permissionParts) === 3) {
            [$module, $action, $resource] = $permissionParts;
            
            // Check module.manage.resource
            $wildcardPermission = "{$module}.manage.{$resource}";
            if ($permissions->contains('name', $wildcardPermission)) {
                return true;
            }
            
            // Check *.manage.all (super admin)
            if ($permissions->contains('name', '*.manage.all')) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Clear permission cache for this user
     */
    public function clearPermissionCache(): void
    {
        Cache::forget("user_permissions_{$this->id}");
    }

    /**
     * Get user's role names
     */
    public function getRoleNames(): Collection
    {
        return $this->roles()->pluck('name');
    }

    // Legacy role methods (for backward compatibility)
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN || $this->hasRole('admin') || $this->hasRole('super_admin');
    }

    public function isProvider()
    {
        return $this->role === self::ROLE_PROVIDER || $this->hasRole('provider');
    }

    public function isCustomer()
    {
        return $this->role === self::ROLE_CUSTOMER || $this->hasRole('customer');
    }

    // Security Methods
    
    /**
     * Check if user account is locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');
        
        // Lock account after 5 failed attempts
        if ($this->failed_login_attempts >= 5) {
            $this->update([
                'locked_until' => now()->addMinutes(15)
            ]);
        }
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Record successful login
     */
    public function recordSuccessfulLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Log user activity
     */
    public function logActivity(string $eventType, array $data = []): void
    {
        AuditLog::create([
            'user_id' => $this->id,
            'event_type' => $eventType,
            'new_values' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    // Relationships
    
    /**
     * Bookings created by this user (customer)
     */
    public function customerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    /**
     * Bookings assigned to this user (provider)
     */
    public function providerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'provider_id');
    }

    /**
     * Services offered by this user (provider)
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'provider_id');
    }

    /**
     * Payments made by this user
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    /**
     * Audit logs for this user
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // Scopes
    
    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for users by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
}