<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:permissions.manage.all')->except(['index', 'show']);
        $this->middleware('permission:permissions.read.all')->only(['index', 'show']);
    }

    /**
     * Display permissions management page
     */
    public function index(): View
    {
        return view('admin.permissions.index');
    }

    /**
     * Get all permissions with pagination and filtering
     */
    public function getData(Request $request): JsonResponse
    {
        $query = Permission::with(['roles:id,name,display_name']);

        // Apply search
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply module filter
        if ($request->filled('module')) {
            $query->where('module', $request->string('module'));
        }

        // Apply action filter
        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        // Apply resource filter
        if ($request->filled('resource')) {
            $query->where('resource', $request->string('resource'));
        }

        $permissions = $query->orderBy('module')
            ->orderBy('action')
            ->orderBy('resource')
            ->paginate($request->integer('per_page', 15));

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
     * Show specific permission
     */
    public function show(Permission $permission): JsonResponse
    {
        $permission->load(['roles:id,name,display_name']);

        return response()->json([
            'success' => true,
            'data' => ['permission' => $permission]
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
            'description' => 'nullable|string|max:500',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permissionData = $validator->validated();
            
            // Generate permission name
            $permissionData['name'] = $permissionData['module'] . '.' . $permissionData['action'] . '.' . $permissionData['resource'];
            
            // Check if permission already exists
            if (Permission::where('name', $permissionData['name'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission already exists'
                ], 422);
            }

            $roles = $permissionData['roles'] ?? [];
            unset($permissionData['roles']);

            $permission = Permission::create($permissionData);

            // Assign to roles if provided
            if (!empty($roles)) {
                $permission->roles()->sync($roles);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'data' => ['permission' => $permission->load('roles')]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update permission
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'module' => 'required|string|max:50',
            'action' => 'required|string|max:50',
            'resource' => 'required|string|max:50',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permissionData = $validator->validated();
            
            // Generate new permission name
            $newName = $permissionData['module'] . '.' . $permissionData['action'] . '.' . $permissionData['resource'];
            
            // Check if new name conflicts with existing permission (except current one)
            if ($newName !== $permission->name && Permission::where('name', $newName)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission name already exists'
                ], 422);
            }

            $permissionData['name'] = $newName;
            $roles = $permissionData['roles'] ?? [];
            unset($permissionData['roles']);

            $permission->update($permissionData);

            // Update roles if provided
            if ($request->has('roles')) {
                $permission->roles()->sync($roles);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'data' => ['permission' => $permission->load('roles')]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete permission
     */
    public function destroy(Permission $permission): JsonResponse
    {
        try {
            $permissionName = $permission->name;
            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permission statistics
     */
    public function getStats(): JsonResponse
    {
        $permissions = Permission::all();
        
        $stats = [
            'total_permissions' => $permissions->count(),
            'total_modules' => $permissions->pluck('module')->unique()->count(),
            'total_actions' => $permissions->pluck('action')->unique()->count(),
            'assigned_permissions' => Permission::whereHas('roles')->count(),
            'modules' => $permissions->pluck('module')->unique()->values()->toArray()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get permissions by module
     */
    public function getByModule(string $module): JsonResponse
    {
        $permissions = Permission::where('module', $module)
            ->orderBy('action')
            ->orderBy('resource')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['permissions' => $permissions]
        ]);
    }

    /**
     * Bulk create permissions
     */
    public function bulkCreate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'modules' => 'required|array',
            'modules.*' => 'string|max:50',
            'actions' => 'required|array',
            'actions.*' => 'string|max:50',
            'resources' => 'required|array',
            'resources.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $createdCount = 0;
            $skippedCount = 0;

            foreach ($request->modules as $module) {
                foreach ($request->actions as $action) {
                    foreach ($request->resources as $resource) {
                        $name = "{$module}.{$action}.{$resource}";
                        
                        // Skip if permission already exists
                        if (Permission::where('name', $name)->exists()) {
                            $skippedCount++;
                            continue;
                        }

                        Permission::create([
                            'name' => $name,
                            'display_name' => ucwords(str_replace('_', ' ', "{$action} {$module} {$resource}")),
                            'module' => $module,
                            'action' => $action,
                            'resource' => $resource,
                            'description' => "Allow {$action} access to {$resource} {$module}"
                        ]);

                        $createdCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Created {$createdCount} permissions, skipped {$skippedCount} existing ones"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk create permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}