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

    // Legacy role methods (for backward compatibility)
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN || $this->hasRole('admin');
    }

    public function isCustomer()
    {
        return $this->role === self::ROLE_CUSTOMER || $this->hasRole('customer');
    }

    public function isProvider()
    {
        return $this->role === self::ROLE_PROVIDER || $this->hasRole('provider');
    }

    // Existing relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function providerBookings()
    {
        return $this->hasMany(Booking::class, 'provider_id');
    }

    // New role management relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot('assigned_at', 'assigned_by')
                    ->withTimestamps();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
    }

    // Role management methods
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        $cacheKey = "user_permissions_{$this->id}";
        
        $permissions = Cache::remember($cacheKey, 3600, function () {
            return $this->getAllPermissions();
        });
        
        return $permissions->contains('name', $permission);
    }

    public function assignRole(string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::findByName($role) : $role;
        
        if ($roleModel && !$this->hasRole($roleModel->name)) {
            $this->roles()->attach($roleModel->id, [
                'assigned_at' => now(),
                'assigned_by' => auth()->id()
            ]);
            
            $this->clearPermissionCache();
            $this->logActivity('role_assigned', ['role' => $roleModel->name]);
        }
    }

    public function removeRole(string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::findByName($role) : $role;
        
        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
            $this->clearPermissionCache();
            $this->logActivity('role_removed', ['role' => $roleModel->name]);
        }
    }

    public function syncRoles(array $roles): void
    {
        $roleIds = collect($roles)->map(function ($role) {
            return is_string($role) ? Role::findByName($role)?->id : $role->id;
        })->filter()->toArray();
        
        $this->roles()->sync($roleIds);
        $this->clearPermissionCache();
        $this->logActivity('roles_synced', ['roles' => $roles]);
    }

    public function getAllPermissions(): Collection
    {
        return $this->roles()
                    ->with('permissions')
                    ->get()
                    ->pluck('permissions')
                    ->flatten()
                    ->unique('id');
    }

    public function getPermissionNames(): Collection
    {
        return $this->getAllPermissions()->pluck('name');
    }

    // API Token management
    public function generateApiToken(string $name, array $abilities = ['*']): string
    {
        $token = $this->createToken($name, $abilities);
        
        $this->logActivity('api_token_created', [
            'token_name' => $name,
            'abilities' => $abilities
        ]);
        
        return $token->plainTextToken;
    }

    public function revokeApiToken(string $tokenId): bool
    {
        $token = $this->tokens()->find($tokenId);
        
        if ($token) {
            $token->delete();
            $this->logActivity('api_token_revoked', ['token_id' => $tokenId]);
            return true;
        }
        
        return false;
    }

    public function revokeAllTokens(): void
    {
        $this->tokens()->delete();
        $this->logActivity('all_api_tokens_revoked');
    }

    // Password management
    public function updatePassword(string $password): void
    {
        // Store current password in history
        $this->passwordHistories()->create([
            'password_hash' => $this->password
        ]);
        
        // Clean up old password history (keep only last 5)
        $this->passwordHistories()
             ->orderBy('created_at', 'desc')
             ->skip(5)
             ->delete();
        
        // Update password
        $this->update(['password' => Hash::make($password)]);
        
        // Revoke all tokens except current session
        $currentToken = $this->currentAccessToken();
        $this->tokens()->when($currentToken, function ($query) use ($currentToken) {
            return $query->where('id', '!=', $currentToken->id);
        })->delete();
        
        $this->logActivity('password_updated');
    }

    public function canReusePassword(string $password): bool
    {
        $recentPasswords = $this->passwordHistories()
                               ->orderBy('created_at', 'desc')
                               ->limit(5)
                               ->pluck('password_hash');
        
        foreach ($recentPasswords as $hash) {
            if (Hash::check($password, $hash)) {
                return false;
            }
        }
        
        return true;
    }

    // Authentication helpers
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');
        
        // Lock account after 5 failed attempts
        if ($this->failed_login_attempts >= 5) {
            $this->update(['locked_until' => now()->addMinutes(15)]);
        }
        
        $this->logActivity('failed_login_attempt');
    }

    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    public function recordSuccessfulLogin(string $ipAddress): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
        
        $this->logActivity('successful_login', ['ip_address' => $ipAddress]);
    }

    // Audit logging
    public function logActivity(string $event, array $data = []): void
    {
        AuditLog::create([
            'user_id' => $this->id,
            'event_type' => $event,
            'new_values' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    // Cache management
    public function clearPermissionCache(): void
    {
        Cache::forget("user_permissions_{$this->id}");
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotLocked($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('locked_until')
              ->orWhere('locked_until', '<', now());
        });
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at')
                    ->whereNotNull('phone_verified_at');
    }

    public function scopeProviders($query)
    {
        return $query->where('role', 'provider');
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available');
    }

    public function scopeWithinRadius($query, float $latitude, float $longitude, float $radiusKm = 10)
    {
        return $query->whereRaw(
            "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?",
            [$latitude, $longitude, $latitude, $radiusKm]
        );
    }

    // Helper methods for new fields
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }

    public function isPhoneVerified(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    public function isIdentityVerified(): bool
    {
        return !is_null($this->identity_verified_at);
    }

    public function isBackgroundCheckApproved(): bool
    {
        return $this->background_check_status === 'approved';
    }

    public function isFullyVerified(): bool
    {
        return $this->isPhoneVerified() && 
               $this->isIdentityVerified() && 
               ($this->background_check_status === 'approved' || $this->background_check_status === 'not_required');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->total_reviews > 0 ? round($this->rating, 2) : 0.0;
    }

    public function getCompletionRateAttribute(): float
    {
        return $this->total_bookings > 0 
            ? round(($this->completed_bookings / $this->total_bookings) * 100, 2) 
            : 0.0;
    }

    public function updateRating(float $newRating): void
    {
        $totalRating = ($this->rating * $this->total_reviews) + $newRating;
        $this->total_reviews += 1;
        $this->rating = $totalRating / $this->total_reviews;
        $this->save();
    }

    public function incrementBookingCount(): void
    {
        $this->increment('total_bookings');
    }

    public function incrementCompletedBookings(): void
    {
        $this->increment('completed_bookings');
    }

    public function setAvailabilityStatus(string $status): void
    {
        if (in_array($status, ['available', 'busy', 'offline'])) {
            $this->update(['availability_status' => $status]);
        }
    }

    public function getNotificationPreference(string $type): bool
    {
        $preferences = $this->notification_preferences ?? [];
        return $preferences[$type] ?? true; // Default to true
    }

    public function setNotificationPreference(string $type, bool $enabled): void
    {
        $preferences = $this->notification_preferences ?? [];
        $preferences[$type] = $enabled;
        $this->update(['notification_preferences' => $preferences]);
    }

    public function getDocumentStatus(string $documentType): ?string
    {
        $documents = $this->documents ?? [];
        return $documents[$documentType]['status'] ?? null;
    }

    public function addDocument(string $type, string $url, string $status = 'pending'): void
    {
        $documents = $this->documents ?? [];
        $documents[$type] = [
            'url' => $url,
            'status' => $status,
            'uploaded_at' => now()->toISOString()
        ];
        $this->update(['documents' => $documents]);
    }

    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getDistanceFrom(float $latitude, float $longitude): float
    {
        if (!$this->hasCoordinates()) {
            return 0;
        }

        $earthRadius = 6371; // km

        $latDelta = deg2rad($latitude - $this->latitude);
        $lonDelta = deg2rad($longitude - $this->longitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
