<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:permissions.manage.all')->except(['index', 'show']);
        $this->middleware('permission:permissions.read.all')->only(['index', 'show']);
    }

    /**
     * Get all permissions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Permission::query();
        
        // Filter by module
        if ($request->has('module')) {
            $query->where('module', $request->string('module'));
        }
        
        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->string('action'));
        }
        
        // Search by name
        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }
        
        $permissions = $query->orderBy('module')
                            ->orderBy('action')
                            ->orderBy('resource')
                            ->paginate($request->integer('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions->items(),
                'pagination' => [
                    'current_page' => $permissions->currentPage(),
                    'last_page' => $permissions->lastPage(),
                    'per_page' => $permissions->perPage(),
                    'total' => $permissions->total()
                ]
            ]
        ]);
    }

    /**
     * Create new permission
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'module' => 'required|string|max:50',
            'action' => 'required|string|max:50',
            'resource' => 'required|string|max:50',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if permission combination is valid
        if (!Permission::isValidCombination($request->module, $request->action, $request->resource)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid permission combination',
                'error_code' => 'INVALID_COMBINATION'
            ], 422);
        }

        try {
            $permission = Permission::createWithValidation([
                'module' => $request->module,
                'action' => $request->action,
                'resource' => $request->resource,
                'display_name' => $request->display_name,
                'description' => $request->description
            ]);

            if (!$permission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create permission - invalid combination',
                    'error_code' => 'CREATION_FAILED'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'data' => [
                    'permission' => $permission
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific permission
     */
    public function show(Permission $permission): JsonResponse
    {
        $permission->load('roles:id,name,display_name');

        return response()->json([
            'success' => true,
            'data' => [
                'permission' => $permission
            ]
        ]);
    }

    /**
     * Update permission
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permission->update($request->only(['display_name', 'description']));

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'data' => [
                    'permission' => $permission->fresh()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete permission
     */
    public function destroy(Permission $permission): JsonResponse
    {
        try {
            // Check if permission is assigned to any roles
            if ($permission->roles()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete permission that is assigned to roles',
                    'error_code' => 'PERMISSION_IN_USE'
                ], 422);
            }

            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permissions grouped by module
     */
    public function getByModule(): JsonResponse
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
     * Get available modules
     */
    public function getModules(): JsonResponse
    {
        $modules = Permission::getAvailableModules();

        return response()->json([
            'success' => true,
            'data' => [
                'modules' => $modules
            ]
        ]);
    }

    /**
     * Get available actions
     */
    public function getActions(): JsonResponse
    {
        $actions = Permission::getAvailableActions();

        return response()->json([
            'success' => true,
            'data' => [
                'actions' => $actions
            ]
        ]);
    }

    /**
     * Bulk create permissions for a module
     */
    public function bulkCreate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'module' => 'required|string|max:50',
            'actions' => 'required|array',
            'actions.*' => 'string|in:create,read,update,delete,manage,view',
            'resources' => 'required|array',
            'resources.*' => 'string|in:own,all'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $createdPermissions = [];
            $skippedPermissions = [];

            foreach ($request->actions as $action) {
                foreach ($request->resources as $resource) {
                    if (Permission::isValidCombination($request->module, $action, $resource)) {
                        $permissionName = "{$request->module}.{$action}.{$resource}";
                        
                        // Check if permission already exists
                        if (Permission::where('name', $permissionName)->exists()) {
                            $skippedPermissions[] = $permissionName;
                            continue;
                        }

                        $permission = Permission::createWithValidation([
                            'module' => $request->module,
                            'action' => $action,
                            'resource' => $resource,
                            'display_name' => ucfirst($action) . ' ' . ucfirst($resource) . ' ' . ucfirst($request->module),
                            'description' => "Allow {$action} access to {$resource} {$request->module}"
                        ]);

                        if ($permission) {
                            $createdPermissions[] = $permission;
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk permission creation completed',
                'data' => [
                    'created_count' => count($createdPermissions),
                    'skipped_count' => count($skippedPermissions),
                    'created_permissions' => $createdPermissions,
                    'skipped_permissions' => $skippedPermissions
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}