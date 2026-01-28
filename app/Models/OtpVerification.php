<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'otp_code',
        'purpose',
        'is_verified',
        'expires_at',
        'attempts'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expires_at' => 'datetime',
        'attempts' => 'integer'
    ];

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is valid
     */
    public function isValid(): bool
    {
        return !$this->is_verified && !$this->isExpired() && $this->attempts < 3;
    }

    /**
     * Mark OTP as verified
     */
    public function markAsVerified(): void
    {
        $this->update(['is_verified' => true]);
    }

    /**
     * Increment attempts
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Scope for active OTPs
     */
    public function scopeActive($query)
    {
        return $query->where('is_verified', false)
                    ->where('expires_at', '>', now())
                    ->where('attempts', '<', 3);
    }

    /**
     * Scope for specific phone and purpose
     */
    public function scopeForPhone($query, string $phone, string $purpose = 'login')
    {
        return $query->where('phone_number', $phone)
                    ->where('purpose', $purpose);
    }

    /**
     * Scope for expired OTPs
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}