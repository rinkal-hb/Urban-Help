<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.manage.all')->except(['index', 'show']);
        $this->middleware('permission:users.read.all')->only(['index', 'show']);
    }

    /**
     * Display users management page
     */
    public function index(): View
    {
        return view('admin.users.index');
    }

    /**
     * Get all users with pagination and filtering
     */
    public function getData(Request $request): JsonResponse
    {
        $query = User::with(['roles:id,name,display_name']);

        // Apply search
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->string('role'));
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        // Apply verification filter
        if ($request->filled('verification')) {
            $verification = $request->string('verification');
            switch ($verification) {
                case 'email_verified':
                    $query->whereNotNull('email_verified_at');
                    break;
                case 'phone_verified':
                    $query->whereNotNull('phone_verified_at');
                    break;
                case 'identity_verified':
                    $query->whereNotNull('identity_verified_at');
                    break;
                case 'unverified':
                    $query->whereNull('email_verified_at')
                          ->whereNull('phone_verified_at')
                          ->whereNull('identity_verified_at');
                    break;
            }
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'users' => UserResource::collection($users->items()),
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
     * Show specific user
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['roles:id,name,display_name']);

        return response()->json([
            'success' => true,
            'data' => ['user' => new UserResource($user)]
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
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'provider_type' => 'nullable|in:individual,company',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'hourly_rate' => 'nullable|numeric|min:0',
            'business_name' => 'nullable|string|max:255',
            'business_license' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_roles' => 'nullable|array',
            'additional_roles.*' => 'exists:roles,id',
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
            'identity_verified' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userData = $validator->validated();
            $userData['password'] = Hash::make($userData['password']);
            
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = Storage::url($avatarPath);
            }

            // Handle verification timestamps
            if ($userData['email_verified'] ?? false) {
                $userData['email_verified_at'] = now();
            }
            if ($userData['phone_verified'] ?? false) {
                $userData['phone_verified_at'] = now();
            }
            if ($userData['identity_verified'] ?? false) {
                $userData['identity_verified_at'] = now();
            }

            // Remove verification flags and additional roles from user data
            $additionalRoles = $userData['additional_roles'] ?? [];
            unset($userData['additional_roles'], $userData['email_verified'], $userData['phone_verified'], $userData['identity_verified']);

            $user = User::create($userData);

            // Assign primary role
            $primaryRole = Role::where('name', $userData['role'])->first();
            if ($primaryRole) {
                $user->assignRole($primaryRole, auth()->user());
            }

            // Assign additional roles
            foreach ($additionalRoles as $roleId) {
                $role = Role::find($roleId);
                if ($role) {
                    $user->assignRole($role, auth()->user());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => ['user' => new UserResource($user->load('roles'))]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'provider_type' => 'nullable|in:individual,company',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'hourly_rate' => 'nullable|numeric|min:0',
            'business_name' => 'nullable|string|max:255',
            'business_license' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_roles' => 'nullable|array',
            'additional_roles.*' => 'exists:roles,id',
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
            'identity_verified' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userData = $validator->validated();
            
            // Handle password update
            if (!empty($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            } else {
                unset($userData['password']);
            }
            
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar) {
                    $oldPath = str_replace('/storage/', '', $user->avatar);
                    Storage::disk('public')->delete($oldPath);
                }
                
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = Storage::url($avatarPath);
            }

            // Handle verification timestamps
            if (isset($userData['email_verified'])) {
                $userData['email_verified_at'] = $userData['email_verified'] ? now() : null;
            }
            if (isset($userData['phone_verified'])) {
                $userData['phone_verified_at'] = $userData['phone_verified'] ? now() : null;
            }
            if (isset($userData['identity_verified'])) {
                $userData['identity_verified_at'] = $userData['identity_verified'] ? now() : null;
            }

            // Remove verification flags and additional roles from user data
            $additionalRoles = $userData['additional_roles'] ?? [];
            unset($userData['additional_roles'], $userData['email_verified'], $userData['phone_verified'], $userData['identity_verified']);

            $user->update($userData);

            // Update roles
            $user->roles()->detach(); // Remove all current roles
            
            // Assign primary role
            $primaryRole = Role::where('name', $userData['role'])->first();
            if ($primaryRole) {
                $user->assignRole($primaryRole, auth()->user());
            }

            // Assign additional roles
            foreach ($additionalRoles as $roleId) {
                $role = Role::find($roleId);
                if ($role) {
                    $user->assignRole($role, auth()->user());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => ['user' => new UserResource($user->load('roles'))]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deleting super admin
        if ($user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete super admin user'
            ], 403);
        }

        try {
            // Delete avatar if exists
            if ($user->avatar) {
                $avatarPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($avatarPath);
            }

            $userName = $user->name;
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
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
            'new_users' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'admin_users' => User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['super_admin', 'admin']);
            })->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(Request $request, User $user): JsonResponse
    {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'data' => ['is_active' => $user->is_active]
        ]);
    }

    /**
     * Bulk assign role to users
     */
    public function bulkAssignRole(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required|string|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = Role::where('name', $request->role)->first();
            $assignedCount = 0;

            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);
                if ($user && !$user->hasRole($role->name)) {
                    $user->assignRole($role, auth()->user());
                    $assignedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned role to {$assignedCount} users"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign roles: ' . $e->getMessage()
            ], 500);
        }
    }
}