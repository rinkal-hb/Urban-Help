<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'resource_type',
        'resource_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime'
    ];

    public $timestamps = false;

    protected $dates = ['created_at'];

    /**
     * User who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for specific event types
     */
    public function scopeEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for specific resource
     */
    public function scopeForResource($query, string $resourceType, int $resourceId = null)
    {
        $query = $query->where('resource_type', $resourceType);
        
        if ($resourceId) {
            $query->where('resource_id', $resourceId);
        }
        
        return $query;
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted event description
     */
    public function getEventDescriptionAttribute(): string
    {
        return match($this->event_type) {
            'login' => 'User logged in',
            'logout' => 'User logged out',
            'failed_login_attempt' => 'Failed login attempt',
            'password_updated' => 'Password was updated',
            'role_assigned' => 'Role was assigned',
            'role_removed' => 'Role was removed',
            'permission_granted' => 'Permission was granted',
            'permission_revoked' => 'Permission was revoked',
            'api_token_created' => 'API token was created',
            'api_token_revoked' => 'API token was revoked',
            default => ucfirst(str_replace('_', ' ', $this->event_type))
        };
    }
}