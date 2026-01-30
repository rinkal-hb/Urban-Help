<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Handle API login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'token_name' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $rateLimitKey = 'api_login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many login attempts. Please try again later.',
                'error_code' => 'RATE_LIMITED'
            ], 429);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($rateLimitKey, 300);
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'error_code' => 'AUTH_FAILED'
            ], 401);
        }

        $tokenName = $request->token_name ?? 'API Token';
        $token = $user->createToken($tokenName)->plainTextToken;

        RateLimiter::clear($rateLimitKey);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Handle API logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Refresh API token
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $currentToken = $request->user()->currentAccessToken();

        if (!$currentToken) {
            return response()->json([
                'success' => false,
                'message' => 'No active token found',
                'error_code' => 'NO_TOKEN'
            ], 401);
        }

        $user = $request->user();
        $tokenName = $currentToken->name;
        $currentToken->delete();
        $newToken = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $newToken,   
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Send OTP to phone number
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $this->otpService->formatPhoneNumber($request->phone);

        if (!$this->otpService->isValidPhoneNumber($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number format',
                'error_code' => 'INVALID_PHONE'
            ], 422);
        }

        if ($this->otpService->isPhoneBlocked($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number is temporarily blocked',
                'error_code' => 'PHONE_BLOCKED'
            ], 429);
        }

        try {
            $purpose = $request->purpose ?? 'login';
            $this->otpService->generateOtp($phone, $purpose);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'data' => [
                    'phone' => $phone,
                    'expires_in' => 300,
                    'remaining_attempts' => $this->otpService->getRemainingAttempts($phone)
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
                'errors' => $e->errors(),
                'error_code' => 'OTP_SEND_FAILED'
            ], 429);
        }
    }

    /**
     * Verify OTP and authenticate
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
            'token_name' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $this->otpService->formatPhoneNumber($request->phone);
        
        if (!$this->otpService->verifyOtp($phone, $request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP or phone number',
                'error_code' => 'OTP_INVALID'
            ], 401);
        }

        $user = User::where('phone', $phone)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error_code' => 'USER_NOT_FOUND'
            ], 404);
        }

        $tokenName = $request->token_name ?? 'OTP Login Token';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verification successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'roles' => $user->roles->pluck('name')
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'error_code' => 'INVALID_CURRENT_PASSWORD'
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            $token = Str::random(60);
            
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );
            
            // Return the plain token for testing/development
            return response()->json([
                'success' => true,
                'message' => 'Password reset token generated',
                'data' => [
                    'token' => $token,
                    'email' => $request->email
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'If the email exists, a password reset link has been sent'
        ]);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token',
                'error_code' => 'INVALID_RESET_TOKEN'
            ], 422);
        }

        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset token has expired',
                'error_code' => 'TOKEN_EXPIRED'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error_code' => 'USER_NOT_FOUND'
            ], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }

    /**
     * Get user's API tokens
     */
    public function getTokens(Request $request): JsonResponse
    {
        $user = $request->user();
        $tokens = $user->tokens()->select('id', 'name', 'abilities', 'last_used_at', 'created_at')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tokens' => $tokens
            ]
        ]);
    }

    /**
     * Revoke specific token
     */
    public function revokeToken(Request $request, string $tokenId): JsonResponse
    {
        $user = $request->user();
        $token = $user->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found',
                'error_code' => 'TOKEN_NOT_FOUND'
            ], 404);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token revoked successfully'
        ]);
    }

    /**
     * Revoke all tokens
     */
    public function revokeAllTokens(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'All tokens revoked successfully'
        ]);
    }
}