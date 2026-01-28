<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticationService
{
    /**
     * Authenticate user with email and password
     */
    public function authenticateWithCredentials(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return null;
        }
        
        // Check if account is locked
        if ($user->isLocked()) {
            throw ValidationException::withMessages([
                'email' => ['Account is temporarily locked due to too many failed attempts.']
            ]);
        }
        
        // Check if account is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Account is deactivated.']
            ]);
        }
        
        // Verify password
        if (!Hash::check($password, $user->password)) {
            $user->incrementFailedAttempts();
            return null;
        }
        
        // Reset failed attempts and record successful login
        $user->resetFailedAttempts();
        $user->recordSuccessfulLogin(request()->ip());
        
        return $user;
    }

    /**
     * Authenticate user with OTP
     */
    public function authenticateWithOtp(string $phone, string $otp): ?User
    {
        $otpService = app(OtpService::class);
        
        if (!$otpService->verifyOtp($phone, $otp)) {
            return null;
        }
        
        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return null;
        }
        
        // Check if account is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'phone' => ['Account is deactivated.']
            ]);
        }
        
        $user->recordSuccessfulLogin(request()->ip());
        
        return $user;
    }

    /**
     * Generate API token for user
     */
    public function generateApiToken(User $user, string $name, array $abilities = ['*']): string
    {
        // Set token expiration based on user role
        $expiresAt = $this->getTokenExpiration($user);
        
        $token = $user->createToken($name, $abilities, $expiresAt);
        
        $user->logActivity('api_token_created', [
            'token_name' => $name,
            'abilities' => $abilities,
            'expires_at' => $expiresAt
        ]);
        
        return $token->plainTextToken;
    }

    /**
     * Revoke specific token
     */
    public function revokeToken(User $user, string $tokenId): bool
    {
        $token = $user->tokens()->find($tokenId);
        
        if ($token) {
            $token->delete();
            $user->logActivity('api_token_revoked', ['token_id' => $tokenId]);
            return true;
        }
        
        return false;
    }

    /**
     * Revoke all user tokens
     */
    public function revokeAllUserTokens(User $user): void
    {
        $user->tokens()->delete();
        $user->logActivity('all_api_tokens_revoked');
    }

    /**
     * Refresh token (create new one and revoke old)
     */
    public function refreshToken(string $token): ?string
    {
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        
        if (!$accessToken || $accessToken->expires_at->isPast()) {
            return null;
        }
        
        $user = $accessToken->tokenable;
        $tokenName = $accessToken->name;
        $abilities = $accessToken->abilities;
        
        // Create new token
        $newToken = $this->generateApiToken($user, $tokenName, $abilities);
        
        // Revoke old token
        $accessToken->delete();
        
        return $newToken;
    }

    /**
     * Update user password
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        // Validate password strength
        if (!$this->isPasswordStrong($newPassword)) {
            throw ValidationException::withMessages([
                'password' => ['Password must be at least 8 characters and contain uppercase, lowercase, numbers, and special characters.']
            ]);
        }
        
        // Check password history
        if (!$user->canReusePassword($newPassword)) {
            throw ValidationException::withMessages([
                'password' => ['Cannot reuse any of your last 5 passwords.']
            ]);
        }
        
        $user->updatePassword($newPassword);
    }

    /**
     * Initiate password reset
     */
    public function initiatePasswordReset(string $email): bool
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return false;
        }
        
        $token = Password::createToken($user);
        
        // Send password reset email (implement based on your notification system)
        // $user->notify(new PasswordResetNotification($token));
        
        $user->logActivity('password_reset_requested');
        
        return true;
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $password): bool
    {
        $user = $this->getUserByResetToken($token);
        
        if (!$user) {
            return false;
        }
        
        if (!$this->isPasswordStrong($password)) {
            throw ValidationException::withMessages([
                'password' => ['Password must be at least 8 characters and contain uppercase, lowercase, numbers, and special characters.']
            ]);
        }
        
        $user->updatePassword($password);
        Password::deleteToken($user);
        
        return true;
    }

    /**
     * Create web session
     */
    public function createWebSession(User $user, bool $remember = false): void
    {
        Auth::login($user, $remember);
        
        $user->recordSuccessfulLogin(request()->ip());
        
        // Set session security
        session()->regenerate();
        
        if ($remember) {
            $user->logActivity('web_login_with_remember');
        } else {
            $user->logActivity('web_login');
        }
    }

    /**
     * Destroy web session
     */
    public function destroyWebSession(): void
    {
        if (Auth::check()) {
            Auth::user()->logActivity('web_logout');
        }
        
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    /**
     * Check if password meets strength requirements
     */
    protected function isPasswordStrong(string $password): bool
    {
        return strlen($password) >= 8 &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[0-9]/', $password) &&
               preg_match('/[^a-zA-Z0-9]/', $password);
    }

    /**
     * Get token expiration based on user role
     */
    protected function getTokenExpiration(User $user): ?\Carbon\Carbon
    {
        // Admin tokens expire in 24 hours
        if ($user->hasRole('admin')) {
            return now()->addHours(24);
        }
        
        // Provider tokens expire in 12 hours
        if ($user->hasRole('provider')) {
            return now()->addHours(12);
        }
        
        // Customer tokens expire in 8 hours
        return now()->addHours(8);
    }

    /**
     * Get user by password reset token
     */
    protected function getUserByResetToken(string $token): ?User
    {
        $email = Password::getRepository()->exists(null, $token);
        
        if (!$email) {
            return null;
        }
        
        return User::where('email', $email)->first();
    }

    /**
     * Check rate limiting for authentication attempts
     */
    public function checkRateLimit(string $key, int $maxAttempts = 5, int $decayMinutes = 15): bool
    {
        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }

    /**
     * Hit rate limiter
     */
    public function hitRateLimit(string $key, int $decayMinutes = 15): void
    {
        RateLimiter::hit($key, $decayMinutes * 60);
    }

    /**
     * Clear rate limiter
     */
    public function clearRateLimit(string $key): void
    {
        RateLimiter::clear($key);
    }

    /**
     * Get rate limit key for IP
     */
    public function getRateLimitKey(string $identifier): string
    {
        return 'auth_attempts:' . $identifier;
    }
}