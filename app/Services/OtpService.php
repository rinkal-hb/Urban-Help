<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OtpService
{
    /**
     * Generate OTP for phone number
     */
    public function generateOtp(string $phone, string $purpose = 'login'): string
    {
        // Check rate limiting
        if ($this->isRateLimited($phone)) {
            throw ValidationException::withMessages([
                'phone' => ['Too many OTP requests. Please try again later.']
            ]);
        }
        
        // Generate 6-digit OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Clean up old OTPs for this phone
        OtpVerification::forPhone($phone, $purpose)->delete();
        
        // Create new OTP record
        OtpVerification::create([
            'phone_number' => $phone,
            'otp_code' => $otp,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0
        ]);
        
        // Send OTP via SMS
        $this->sendOtp($phone, $otp);
        
        // Update rate limiting
        $this->updateRateLimit($phone);
        
        Log::info('OTP generated', [
            'phone' => $phone,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(5)
        ]);
        
        return $otp;
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(string $phone, string $otp, string $purpose = 'login'): bool
    {
        $otpRecord = OtpVerification::forPhone($phone, $purpose)
                                  ->active()
                                  ->first();
        
        if (!$otpRecord) {
            Log::warning('OTP verification failed - no active OTP found', [
                'phone' => $phone,
                'purpose' => $purpose
            ]);
            return false;
        }
        
        // Check if OTP matches
        if ($otpRecord->otp_code !== $otp) {
            $otpRecord->incrementAttempts();
            
            // Block phone if too many attempts
            if ($otpRecord->attempts >= 3) {
                $this->blockPhone($phone);
                Log::warning('Phone blocked due to too many OTP attempts', [
                    'phone' => $phone,
                    'attempts' => $otpRecord->attempts
                ]);
            }
            
            return false;
        }
        
        // Mark as verified
        $otpRecord->markAsVerified();
        
        // Clear rate limiting on successful verification
        $this->clearAttempts($phone);
        
        Log::info('OTP verified successfully', [
            'phone' => $phone,
            'purpose' => $purpose
        ]);
        
        return true;
    }

    /**
     * Send OTP via SMS (implement based on your SMS provider)
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        try {
            // Implement SMS sending logic here
            // Example with a generic SMS service:
            
            $message = "Your verification code is: {$otp}. Valid for 5 minutes. Do not share this code.";
            
            // For development/testing, you might want to log the OTP
            if (app()->environment('local', 'testing')) {
                Log::info("OTP for {$phone}: {$otp}");
                return true;
            }
            
            // Implement actual SMS sending here
            // $smsService = app(SmsService::class);
            // return $smsService->send($phone, $message);
            
            // For now, return true (implement actual SMS service)
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Check if phone is rate limited
     */
    public function isRateLimited(string $phone): bool
    {
        $key = $this->getRateLimitKey($phone);
        $attempts = Cache::get($key, 0);
        
        return $attempts >= 3;
    }

    /**
     * Check if phone is blocked
     */
    public function isPhoneBlocked(string $phone): bool
    {
        $blockKey = $this->getBlockKey($phone);
        return Cache::has($blockKey);
    }

    /**
     * Block phone for 15 minutes
     */
    public function blockPhone(string $phone): void
    {
        $blockKey = $this->getBlockKey($phone);
        Cache::put($blockKey, true, now()->addMinutes(15));
    }

    /**
     * Update rate limiting
     */
    public function updateRateLimit(string $phone): void
    {
        $key = $this->getRateLimitKey($phone);
        $attempts = Cache::get($key, 0) + 1;
        
        // Rate limit for 15 minutes
        Cache::put($key, $attempts, now()->addMinutes(15));
    }

    /**
     * Increment attempts for rate limiting
     */
    public function incrementAttempts(string $phone): void
    {
        $this->updateRateLimit($phone);
    }

    /**
     * Clear attempts (on successful verification)
     */
    public function clearAttempts(string $phone): void
    {
        $key = $this->getRateLimitKey($phone);
        $blockKey = $this->getBlockKey($phone);
        
        Cache::forget($key);
        Cache::forget($blockKey);
    }

    /**
     * Clean up expired OTPs
     */
    public function cleanupExpiredOtps(): void
    {
        $deletedCount = OtpVerification::expired()->delete();
        
        Log::info('Cleaned up expired OTPs', [
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Get rate limit cache key
     */
    protected function getRateLimitKey(string $phone): string
    {
        return "otp_rate_limit:{$phone}";
    }

    /**
     * Get block cache key
     */
    protected function getBlockKey(string $phone): string
    {
        return "otp_blocked:{$phone}";
    }

    /**
     * Get remaining attempts for phone
     */
    public function getRemainingAttempts(string $phone): int
    {
        $key = $this->getRateLimitKey($phone);
        $attempts = Cache::get($key, 0);
        
        return max(0, 3 - $attempts);
    }

    /**
     * Get time until rate limit resets
     */
    public function getRateLimitResetTime(string $phone): ?\Carbon\Carbon
    {
        $key = $this->getRateLimitKey($phone);
        
        if (!Cache::has($key)) {
            return null;
        }
        
        // Get TTL from cache (this is implementation dependent)
        // For simplicity, return 15 minutes from now
        return now()->addMinutes(15);
    }

    /**
     * Validate phone number format
     */
    public function isValidPhoneNumber(string $phone): bool
    {
        // Basic phone validation - adjust based on your requirements
        return preg_match('/^\+?[1-9]\d{1,14}$/', $phone);
    }

    /**
     * Format phone number
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Remove all non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Add + if not present and doesn't start with 0
        if (!str_starts_with($phone, '+') && !str_starts_with($phone, '0')) {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }
}