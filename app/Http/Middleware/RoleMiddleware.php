<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!auth()->check()) {
            return $this->handleUnauthorized($request, 'Authentication required');
        }

        $user = auth()->user();

        // Check if user is active
        if (!$user->is_active) {
            return $this->handleUnauthorized($request, 'Account is deactivated');
        }

        // Check if user is locked
        if ($user->isLocked()) {
            return $this->handleUnauthorized($request, 'Account is temporarily locked');
        }

        // Check if user has any of the required roles
        if (!$this->hasRequiredRole($user, $roles)) {
            return $this->handleUnauthorized($request, 'Insufficient role permissions');
        }

        return $next($request);
    }

    /**
     * Check if user has any of the required roles
     */
    protected function hasRequiredRole($user, array $roles): bool
    {
        // Support both new role system and legacy role field
        foreach ($roles as $role) {
            // Check new role system
            if ($user->hasRole($role)) {
                return true;
            }
            
            // Check legacy role field for backward compatibility
            if (isset($user->role) && $user->role === $role) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle unauthorized access
     */
    protected function handleUnauthorized(Request $request, string $message = 'Unauthorized access'): Response
    {
        // Log the unauthorized access attempt
        if (auth()->check()) {
            auth()->user()->logActivity('unauthorized_access_attempt', [
                'route' => $request->route()?->getName(),
                'url' => $request->url(),
                'method' => $request->method(),
                'required_roles' => func_get_args()[1] ?? []
            ]);
        }

        // Return JSON response for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'INSUFFICIENT_ROLE_PERMISSIONS'
            ], 403);
        }

        // Return HTTP 403 for web requests
        abort(403, $message);
    }
}