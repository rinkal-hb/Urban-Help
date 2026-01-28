<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthenticationService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected AuthenticationService $authService;
    protected OtpService $otpService;

    public function __construct(AuthenticationService $authService, OtpService $otpService)
    {
        $this->authService = $authService;
        $this->otpService = $otpService;
    }

    // Web Authentication Methods

    /**
     * Show login form
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle web login
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        $rateLimitKey = $this->authService->getRateLimitKey($request->ip());

        if ($this->authService->checkRateLimit($rateLimitKey)) {
            throw ValidationException::withMessages([
                'email' => ['Too many login attempts. Please try again later.']
            ]);
        }

        try {
            $user = $this->authService->authenticateWithCredentials(
                $request->email,
                $request->password
            );

            if (!$user) {
                $this->authService->hitRateLimit($rateLimitKey);
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.']
                ]);
            }

            $this->authService->createWebSession($user, $request->boolean('remember'));
            $this->authService->clearRateLimit($rateLimitKey);

            // Ensure user has admin role if they don't have any roles
            if (!$user->roles()->exists() && in_array($user->role, ['admin', 'super_admin'])) {
                $user->assignRole($user->role);
            }

            return redirect()->intended('admin/dashboard');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput($request->except('password'));
        }
    }

    /**
     * Handle web logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->destroyWebSession();
        return redirect('/');
    }

    // API Authentication Methods

    /**
     * Handle API login
     */
    public function apiLogin(Request $request): JsonResponse
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

        $rateLimitKey = $this->authService->getRateLimitKey($request->ip());

        if ($this->authService->checkRateLimit($rateLimitKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many login attempts. Please try again later.',
                'error_code' => 'RATE_LIMITED'
            ], 429);
        }

        try {
            $user = $this->authService->authenticateWithCredentials(
                $request->email,
                $request->password
            );

            if (!$user) {
                $this->authService->hitRateLimit($rateLimitKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'error_code' => 'AUTH_FAILED'
                ], 401);
            }

            $tokenName = $request->token_name ?? 'API Token';
            $token = $this->authService->generateApiToken($user, $tokenName);

            $this->authService->clearRateLimit($rateLimitKey);

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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'errors' => $e->errors(),
                'error_code' => 'AUTH_FAILED'
            ], 401);
        }
    }

    /**
     * Handle API logout
     */
    public function apiLogout(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
            $user->logActivity('api_logout');
        }

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

        $newToken = $this->authService->refreshToken($currentToken->token);

        if (!$newToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
                'error_code' => 'REFRESH_FAILED'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $newToken,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    // OTP Authentication Methods

    /**
     * Send OTP to phone number
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'purpose' => 'string|in:login,registration,password_reset'
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
                    'expires_in' => 300, // 5 minutes
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
            'purpose' => 'string|in:login,registration,password_reset',
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
        $purpose = $request->purpose ?? 'login';

        try {
            $user = $this->authService->authenticateWithOtp($phone, $request->otp);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP or phone number',
                    'error_code' => 'OTP_INVALID'
                ], 401);
            }

            $tokenName = $request->token_name ?? 'OTP Login Token';
            $token = $this->authService->generateApiToken($user, $tokenName);

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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed',
                'errors' => $e->errors(),
                'error_code' => 'OTP_VERIFICATION_FAILED'
            ], 401);
        }
    }

    // Password Management Methods

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

        // Verify current password
        if (!password_verify($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'error_code' => 'INVALID_CURRENT_PASSWORD'
            ], 422);
        }

        try {
            $this->authService->updatePassword($user, $request->new_password);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password update failed',
                'errors' => $e->errors(),
                'error_code' => 'PASSWORD_UPDATE_FAILED'
            ], 422);
        }
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

        $success = $this->authService->initiatePasswordReset($request->email);

        // Always return success for security (don't reveal if email exists)
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

        try {
            $success = $this->authService->resetPassword($request->token, $request->password);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token',
                    'error_code' => 'INVALID_RESET_TOKEN'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed',
                'errors' => $e->errors(),
                'error_code' => 'PASSWORD_RESET_FAILED'
            ], 422);
        }
    }

    // Token Management Methods

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
        $success = $this->authService->revokeToken($user, $tokenId);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found',
                'error_code' => 'TOKEN_NOT_FOUND'
            ], 404);
        }

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
        $user = $request->user();
        $this->authService->revokeAllUserTokens($user);

        return response()->json([
            'success' => true,
            'message' => 'All tokens revoked successfully'
        ]);
    }
}
