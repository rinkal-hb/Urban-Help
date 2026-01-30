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
use Illuminate\View\View;

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
     * Display roles management page
     */
    public function index(): View
    {
        return view('admin.roles.index');
    }

    /**
     * Get all roles with pagination and filtering (for web DataTables)
     */
    public function getData(Request $request)
    {
        if ($request->expectsJson()) {
            return $this->getDataJson($request);
        }

        // For web requests, return JSON for DataTables
        return $this->getDataJson($request);
    }

    /**
     * Get roles data in JSON format for DataTables
     */
    protected function getDataJson(Request $request): JsonResponse
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search.value', '');

        $query = Role::with(['permissions:id,name,display_name']);

        // Apply search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Get total count before pagination
        $totalRecords = $query->count();

        // Apply pagination
        $roles = $query->orderBy('hierarchy_level', 'desc')
            ->orderBy('name')
            ->skip($start)
            ->take($length)
            ->get();

        // Format data for DataTables
        $data = [];
        foreach ($roles as $role) {
            $systemRole = in_array($role->name, ['super_admin', 'admin', 'customer', 'provider']);

            $statusBadge = $role->is_active
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            $actions = '<div class="btn-group">';
            $actions .= '<button class="btn btn-sm btn-info" onclick="viewRole(' . $role->id . ')" title="View"><i class="ri-eye-line"></i></button>';
            $actions .= '<button class="btn btn-sm btn-primary" onclick="editRole(' . $role->id . ')" title="Edit"><i class="ri-edit-line"></i></button>';

            if (!$systemRole) {
                $actions .= '<button class="btn btn-sm btn-danger" onclick="deleteRole(' . $role->id . ', \'' . $role->name . '\')" title="Delete"><i class="ri-delete-line"></i></button>';
            }
            $actions .= '</div>';

            $data[] = [
                'id' => $role->id,
                'name' => $role->name . ($systemRole ? ' <span class="badge bg-warning ms-1">System</span>' : ''),
                'display_name' => $role->display_name,
                'hierarchy_level' => '<span class="badge bg-primary">' . $role->hierarchy_level . '</span>',
                'permissions_count' => '<span class="badge bg-info">' . $role->permissions->count() . '</span>',
                'users_count' => '<span class="badge bg-secondary">' . $role->users()->count() . '</span>',
                'status' => $statusBadge,
                'actions' => $actions
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Show specific role
     */
    public function show(Role $role): JsonResponse
    {
        $role->load(['permissions:id,name,display_name,module,action,resource']);

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $role,
                'users_count' => $role->users()->count(),
                'permissions_count' => $role->permissions()->count()
            ]
        ]);
    }

    /**
     * Create new role
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'hierarchy_level' => 'integer|min:0|max:99', // Reserve 100 for super admin
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
            $roleData = $validator->validated();
            $permissions = $roleData['permissions'] ?? [];
            unset($roleData['permissions']);

            $role = Role::create($roleData);

            // Assign permissions if provided
            if (!empty($permissions)) {
                $role->permissions()->sync($permissions);
            }

            // Log the activity
            auth()->user()->logActivity('role_created', [
                'role_name' => $role->name,
                'permissions_count' => count($permissions)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => ['role' => $role->load('permissions')]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        // Prevent updating super admin role
        if ($role->name === 'super_admin' && !auth()->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify super admin role'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-z_]+$/|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'hierarchy_level' => 'integer|min:0|max:99',
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
            $roleData = $validator->validated();
            $permissions = $roleData['permissions'] ?? [];
            unset($roleData['permissions']);

            $role->update($roleData);

            // Update permissions if provided
            if ($request->has('permissions')) {
                $role->permissions()->sync($permissions);
            }

            // Clear permission cache for all users with this role
            $role->users()->each(function ($user) {
                $user->clearPermissionCache();
            });

            // Log the activity
            auth()->user()->logActivity('role_updated', [
                'role_name' => $role->name,
                'permissions_count' => count($permissions)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => ['role' => $role->load('permissions')]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete role
     */
    public function destroy(Role $role): JsonResponse
    {
        // Prevent deleting system roles
        $systemRoles = ['super_admin', 'admin', 'customer', 'provider'];
        if (in_array($role->name, $systemRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete system role'
            ], 403);
        }

        // Check if role has users
        if ($role->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role that has assigned users'
            ], 422);
        }

        try {
            $roleName = $role->name;
            $role->delete();

            // Log the activity
            auth()->user()->logActivity('role_deleted', [
                'role_name' => $roleName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage()
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
            ->orderBy('module')
            ->orderBy('action')
            ->get()
            ->groupBy('module');

        return response()->json([
            'success' => true,
            'data' => ['permissions' => $permissions]
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
            $role->permissions()->sync($request->permissions);

            // Clear permission cache for all users with this role
            $role->users()->each(function ($user) {
                $user->clearPermissionCache();
            });

            // Log the activity
            auth()->user()->logActivity('role_permissions_updated', [
                'role_name' => $role->name,
                'permissions_count' => count($request->permissions)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users with this role
     */
    public function getUsers(Request $request, Role $role): JsonResponse
    {
        $users = $role->users()
            ->select('id', 'name', 'email', 'is_active', 'last_login_at')
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate($request->integer('per_page', 15));

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
            $assignedCount = 0;
            $currentUser = auth()->user();

            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);
                if ($user && !$user->hasRole($role->name)) {
                    $user->assignRole($role, $currentUser);
                    $assignedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned {$assignedCount} users to role"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign users: ' . $e->getMessage()
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
            $removedCount = 0;

            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);
                if ($user && $user->hasRole($role->name)) {
                    $user->removeRole($role);
                    $removedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully removed {$removedCount} users from role"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available permissions grouped by module
     */
    public function getAvailablePermissions(): JsonResponse
    {
        $permissions = Permission::select('id', 'name', 'display_name', 'module', 'action', 'resource')
            ->orderBy('module')
            ->orderBy('action')
            ->get()
            ->groupBy('module');

        return response()->json([
            'success' => true,
            'data' => ['permissions' => $permissions]
        ]);
    }
}
