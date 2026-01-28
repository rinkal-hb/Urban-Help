<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\PermissionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PermissionMiddleware
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$permissions): mixed
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

        // Check if user has any of the required permissions
        if (!$this->hasRequiredPermission($user, $permissions)) {
            return $this->handleUnauthorized($request, 'Insufficient permissions');
        }

        return $next($request);
    }

    /**
     * Check if user has any of the required permissions
     */
    protected function hasRequiredPermission($user, array $permissions): bool
    {
        $userPermissions = $this->getCachedPermissions($user);

        foreach ($permissions as $permission) {
            // Direct permission check
            if ($this->permissionService->checkPermission($user, $permission)) {
                return true;
            }

            // Check for wildcard permissions
            if ($this->checkWildcardPermission($userPermissions, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get cached user permissions
     */
    protected function getCachedPermissions($user): Collection
    {
        $cacheKey = "user_permissions_{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            return $this->permissionService->getUserPermissions($user);
        });
    }

    /**
     * Check wildcard permissions
     */
    protected function checkWildcardPermission(Collection $userPermissions, string $requiredPermission): bool
    {
        $parts = explode('.', $requiredPermission);
        
        if (count($parts) !== 3) {
            return false;
        }

        [$module, $action, $resource] = $parts;

        // Check for module.manage.all permission
        $manageAllPermission = "{$module}.manage.all";
        if ($userPermissions->contains('name', $manageAllPermission)) {
            return true;
        }

        // Check for module.manage.resource permission
        $manageResourcePermission = "{$module}.manage.{$resource}";
        if ($userPermissions->contains('name', $manageResourcePermission)) {
            return true;
        }

        // Check for *.manage.all (super admin permission)
        if ($userPermissions->contains('name', '*.manage.all')) {
            return true;
        }

        return false;
    }

    /**
     * Handle unauthorized access
     */
    protected function handleUnauthorized(Request $request, string $message = 'Insufficient permissions'): Response
    {
        // Log the unauthorized access attempt
        if (auth()->check()) {
            auth()->user()->logActivity('unauthorized_access_attempt', [
                'route' => $request->route()?->getName(),
                'url' => $request->url(),
                'method' => $request->method(),
                'required_permissions' => func_get_args()[1] ?? [],
                'user_permissions' => auth()->user()->getPermissionNames()->toArray()
            ]);
        }

        // Return JSON response for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'INSUFFICIENT_PERMISSIONS'
            ], 403);
        }

        // Return HTTP 403 for web requests
        abort(403, $message);
    }

    /**
     * Check if user can perform action on resource
     */
    public static function canPerformAction(string $module, string $action, string $resource = 'all'): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $permission = "{$module}.{$action}.{$resource}";
        $permissionService = app(PermissionService::class);
        
        return $permissionService->checkPermission(auth()->user(), $permission);
    }

    /**
     * Check if user can manage specific module
     */
    public static function canManage(string $module): bool
    {
        return self::canPerformAction($module, 'manage', 'all');
    }

    /**
     * Check if user can view specific module
     */
    public static function canView(string $module): bool
    {
        return self::canPerformAction($module, 'view', 'all') || 
               self::canPerformAction($module, 'read', 'all');
    }

    /**
     * Check if user can create in specific module
     */
    public static function canCreate(string $module): bool
    {
        return self::canPerformAction($module, 'create', 'all');
    }

    /**
     * Check if user can update in specific module
     */
    public static function canUpdate(string $module): bool
    {
        return self::canPerformAction($module, 'update', 'all');
    }

    /**
     * Check if user can delete in specific module
     */
    public static function canDelete(string $module): bool
    {
        return self::canPerformAction($module, 'delete', 'all');
    }
}