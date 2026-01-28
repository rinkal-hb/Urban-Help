<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\PermissionService;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
        
        $this->middleware('auth');
        $this->middleware('permission:users.manage.all')->except(['index', 'show']);
        $this->middleware('permission:users.read.all')->only(['index', 'show']);
    }

    /**
     * Display users page
     */
    public function index(Request $request)
    {
        return view('admin.users.index');
    }

    /**
     * Get users data for DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        $draw = filter_var($request->get('draw'), FILTER_VALIDATE_INT);
        $start = filter_var($request->get('start'), FILTER_VALIDATE_INT);
        $rowperpage = filter_var($request->get('length'), FILTER_VALIDATE_INT);

        $order_arr = $request->get('order', []);
        $searchValue = $request->get('search')['value'] ?? '';

        $columnIndex = $order_arr[0]['column'] ?? 0;
        $columns = ['id', 'name', 'email', 'role', 'is_active'];
        $columnName = $columns[$columnIndex] ?? 'id';
        $columnSortOrder = $order_arr[0]['dir'] ?? 'desc';

        $query = User::with(['roles:id,name,display_name']);

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                  ->orWhere('email', 'like', '%' . $searchValue . '%')
                  ->orWhere('phone', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = $query->count();

        $users = $query->orderBy($columnName, $columnSortOrder)
                      ->skip($start)
                      ->take($rowperpage)
                      ->get();

        $data_arr = [];
        foreach ($users as $index => $user) {
            $data_arr[] = [
                'id' => $user->id,
                'no' => $start + $index + 1,
                'name' => $user->name,
                'email' => $user->email,
                'role' => '<span class="badge bg-primary">' . ucfirst($user->role) . '</span>',
                'status' => $user->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>',
                'action' => '<button class="btn btn-sm btn-primary" onclick="editUser(' . $user->id . ')">Edit</button> <button class="btn btn-sm btn-danger" onclick="deleteUser(' . $user->id . ')">Delete</button>'
            ];
        }

        return response()->json([
            'draw' => $draw,
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecords,
            'data' => $data_arr
        ]);
    }

    /**
     * Create new user
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,customer,provider',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'roles' => 'array',
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
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country ?? 'India',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);

            // Assign roles if provided
            if ($request->has('roles')) {
                foreach ($request->roles as $roleId) {
                    $role = Role::find($roleId);
                    if ($role) {
                        $this->permissionService->assignRoleToUser($user, $role);
                    }
                }
            }

            $user->load('roles:id,name,display_name');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'user' => new UserResource($user)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific user
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['roles:id,name,display_name']);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user)
            ]
        ]);
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => [
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => [
                'string',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8',
            'role' => 'in:admin,customer,provider',
            'is_active' => 'boolean',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only([
                'name', 'email', 'phone', 'role', 'is_active',
                'city', 'state', 'country'
            ]);

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            $user->load('roles:id,name,display_name');

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => [
                    'user' => new UserResource($user)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            // Prevent deletion of super admin
            if ($user->hasRole('super_admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete super admin user',
                    'error_code' => 'SUPER_ADMIN_DELETION'
                ], 422);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign roles to user
     */
    public function assignRoles(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $roles = Role::whereIn('id', $request->role_ids)->get();
            $assignedCount = 0;

            foreach ($roles as $role) {
                if (!$user->hasRole($role->name)) {
                    $this->permissionService->assignRoleToUser($user, $role);
                    $assignedCount++;
                }
            }

            $user->load('roles:id,name,display_name');

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned {$assignedCount} roles to user",
                'data' => [
                    'user' => new UserResource($user),
                    'assigned_count' => $assignedCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove roles from user
     */
    public function removeRoles(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $roles = Role::whereIn('id', $request->role_ids)->get();
            $removedCount = 0;

            foreach ($roles as $role) {
                // Prevent removal of super_admin role
                if ($role->name === 'super_admin') {
                    continue;
                }

                if ($user->hasRole($role->name)) {
                    $this->permissionService->removeRoleFromUser($user, $role);
                    $removedCount++;
                }
            }

            $user->load('roles:id,name,display_name');

            return response()->json([
                'success' => true,
                'message' => "Successfully removed {$removedCount} roles from user",
                'data' => [
                    'user' => new UserResource($user),
                    'removed_count' => $removedCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user): JsonResponse
    {
        try {
            // Prevent deactivation of super admin
            if ($user->hasRole('super_admin') && $user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot deactivate super admin user',
                    'error_code' => 'SUPER_ADMIN_DEACTIVATION'
                ], 422);
            }

            $user->update(['is_active' => !$user->is_active]);

            return response()->json([
                'success' => true,
                'message' => $user->is_active ? 'User activated successfully' : 'User deactivated successfully',
                'data' => [
                    'user' => new UserResource($user)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle user status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')
                                  ->whereNotNull('phone_verified_at')
                                  ->count(),
            'users_by_role' => User::selectRaw('role, COUNT(*) as count')
                                  ->groupBy('role')
                                  ->pluck('count', 'role'),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats
            ]
        ]);
    }
}