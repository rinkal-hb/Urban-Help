<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
        
        // Apply middleware for role management permissions
        $this->middleware('auth');
        $this->middleware('permission:roles.manage.all')->except(['index', 'show']);
        $this->middleware('permission:roles.read.all')->only(['index', 'show']);
    }

    /**
     * Get all roles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Role::with(['permissions:id,name,display_name']);
        
        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }
        
        // Filter by hierarchy level
        if ($request->has('hierarchy_level')) {
            $query->where('hierarchy_level', '>=', $request->integer('hierarchy_level'));
        }
        
        // Search by name
        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }
        
        $roles = $query->orderBy('hierarchy_level', 'desc')
                      ->orderBy('name')
                      ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'roles' => $roles->items(),
                'pagination' => [
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                    'per_page' => $roles->perPage(),
                    'total' => $roles->total()
                ]
            ]
        ]);
    }

    /**
     * Create new role
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'hierarchy_level' => 'integer|min:0|max:100',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = $this->permissionService->createRole($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => [
                    'role' => $role->load('permissions:id,name,display_name')
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific role
     */
    public function show(Role $role): JsonResponse
    {
        $role->load([
            'permissions:id,name,display_name,module,action,resource',
            'users:id,name,email'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $role
            ]
        ]);
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'hierarchy_level' => 'integer|min:0|max:100',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updatedRole = $this->permissionService->updateRole($role, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => [
                    'role' => $updatedRole->load('permissions:id,name,display_name')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete role
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            $this->permissionService->deleteRole($role);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'ROLE_HAS_USERS'
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role permissions
     */
    public function getPermissions(Role $role): JsonResponse
    {
        $permissions = $role->permissions()
                           ->select('id', 'name', 'display_name', 'module', 'action', 'resource')
                           ->get()
                           ->groupBy('module');

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions
            ]
        ]);
    }

    /**
     * Sync role permissions
     */
    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->permissionService->syncRolePermissions($role, $request->permissions);

            $role->load('permissions:id,name,display_name,module');

            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully',
                'data' => [
                    'role' => $role
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users assigned to role
     */
    public function getUsers(Role $role): JsonResponse
    {
        $users = $role->users()
                     ->select('id', 'name', 'email', 'is_active')
                     ->withPivot('assigned_at', 'assigned_by')
                     ->with('roles:id,name,display_name')
                     ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total()
                ]
            ]
        ]);
    }

    /**
     * Assign users to role
     */
    public function assignUsers(Request $request, Role $role): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $users = User::whereIn('id', $request->user_ids)->get();
            $assignedCount = 0;

            foreach ($users as $user) {
                if (!$user->hasRole($role->name)) {
                    $this->permissionService->assignRoleToUser($user, $role);
                    $assignedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned role to {$assignedCount} users",
                'data' => [
                    'assigned_count' => $assignedCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign users to role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove users from role
     */
    public function removeUsers(Request $request, Role $role): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $users = User::whereIn('id', $request->user_ids)->get();
            $removedCount = 0;

            foreach ($users as $user) {
                if ($user->hasRole($role->name)) {
                    $this->permissionService->removeRoleFromUser($user, $role);
                    $removedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully removed role from {$removedCount} users",
                'data' => [
                    'removed_count' => $removedCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove users from role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available permissions grouped by module
     */
    public function getAvailablePermissions(): JsonResponse
    {
        $permissions = Permission::select('id', 'name', 'display_name', 'module', 'action', 'resource', 'description')
                                ->orderBy('module')
                                ->orderBy('action')
                                ->get()
                                ->groupBy('module');

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions
            ]
        ]);
    }

    /**
     * Get role hierarchy with inherited permissions
     */
    public function getRoleHierarchy(Role $role): JsonResponse
    {
        $hierarchyPermissions = $this->permissionService->getRoleHierarchy($role);
        $directPermissions = $role->permissions;
        $inheritedPermissions = $hierarchyPermissions->diff($directPermissions);

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $role,
                'direct_permissions' => $directPermissions->groupBy('module'),
                'inherited_permissions' => $inheritedPermissions->groupBy('module'),
                'all_permissions' => $hierarchyPermissions->groupBy('module')
            ]
        ]);
    }
}