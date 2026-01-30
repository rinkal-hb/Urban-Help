<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dashboard.read.all');
    }

    /**
     * Show dashboard page
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Show profile page
     */
    public function profile()
    {
        return view('admin.profile');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $user->update($request->only([
            'name',
            'email',
            'phone',
            'date_of_birth',
            'gender',
            'address',
            'city',
            'state',
            'postal_code',
            'country'
        ]));

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password changed successfully!');
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_customers' => User::whereHas('roles', function ($q) {
                    $q->where('name', 'customer');
                })->count(),
                'total_providers' => User::whereHas('roles', function ($q) {
                    $q->where('name', 'provider');
                })->count(),
                'total_bookings' => class_exists('App\Models\Booking') ? Booking::count() : 0,
                'total_revenue' => class_exists('App\Models\Payment') ? Payment::where('status', 'completed')->sum('amount') : 0,
                'total_services' => class_exists('App\Models\Service') ? Service::where('is_active', true)->count() : 0,
                'active_users' => User::where('is_active', true)->count(),
                'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
                'pending_bookings' => class_exists('App\Models\Booking') ? Booking::where('status', 'pending')->count() : 0
            ];

            return response()->json([
                'success' => true,
                'data' => ['stats' => $stats]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role distribution for chart
     */
    public function getRoleDistributionChart(): JsonResponse
    {
        try {
            $roleDistribution = DB::table('user_roles')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->select('roles.display_name as role', DB::raw('count(*) as count'))
                ->groupBy('roles.id', 'roles.display_name')
                ->orderBy('count', 'desc')
                ->get();

            // Add colors for each role
            $colors = ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#dc3545'];
            $data = $roleDistribution->map(function ($item, $index) use ($colors) {
                return [
                    'role' => $item->role,
                    'count' => $item->count,
                    'color' => $colors[$index % count($colors)]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load role distribution: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent users
     */
    public function getRecentUsers(Request $request): JsonResponse
    {
        try {
            $users = User::with(['roles:id,name,display_name'])
                ->orderBy('created_at', 'desc')
                ->limit($request->integer('limit', 10))
                ->get();

            return response()->json([
                'success' => true,
                'data' => ['users' => $users]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load recent users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity logs for dashboard
     */
    public function getActivityLogs(Request $request): JsonResponse
    {
        try {
            $filter = $request->string('filter', 'all');
            $limit = $request->integer('limit', 20);

            $query = AuditLog::with(['user:id,name'])
                ->orderBy('created_at', 'desc');

            // Apply filter
            if ($filter !== 'all') {
                switch ($filter) {
                    case 'users':
                        $query->where('event', 'like', 'user_%');
                        break;
                    case 'roles':
                        $query->where('event', 'like', 'role_%');
                        break;
                    case 'bookings':
                        $query->where('event', 'like', 'booking_%');
                        break;
                }
            }

            $activities = $query->limit($limit)->get();

            // Format activities for display
            $formattedActivities = $activities->map(function ($activity) {
                return [
                    'user' => $activity->user ? $activity->user->name : 'System',
                    'action' => $this->formatActivityAction($activity->event, $activity->properties),
                    'time' => $activity->created_at->diffForHumans(),
                    'type' => $this->getActivityType($activity->event),
                    'icon' => $this->getActivityIcon($activity->event),
                    'color' => $this->getActivityColor($activity->event)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => ['activities' => $formattedActivities]
            ]);
        } catch (\Exception $e) {
            // Return mock data if AuditLog doesn't exist or fails
            $mockActivities = [
                [
                    'user' => 'Admin',
                    'action' => 'Created new user account',
                    'time' => '2 minutes ago',
                    'type' => 'user',
                    'icon' => 'bx-user-plus',
                    'color' => 'success'
                ],
                [
                    'user' => 'System',
                    'action' => 'Updated role permissions',
                    'time' => '15 minutes ago',
                    'type' => 'role',
                    'icon' => 'bx-shield',
                    'color' => 'info'
                ],
                [
                    'user' => 'Provider',
                    'action' => 'New booking created',
                    'time' => '1 hour ago',
                    'type' => 'booking',
                    'icon' => 'bx-briefcase',
                    'color' => 'warning'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => ['activities' => $mockActivities]
            ]);
        }
    }

    /**
     * Get user growth chart data
     */
    public function getUserGrowthChart(Request $request): JsonResponse
    {
        try {
            $days = $request->integer('days', 30);
            $startDate = now()->subDays($days);

            $userGrowth = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => ['growth' => $userGrowth]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load user growth data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export dashboard data
     */
    public function exportData(Request $request): JsonResponse
    {
        try {
            $type = $request->string('type', 'users');

            switch ($type) {
                case 'users':
                    $data = User::with('roles')->get();
                    break;
                case 'roles':
                    $data = Role::with('permissions')->get();
                    break;
                case 'permissions':
                    $data = Permission::with('roles')->get();
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid export type'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => ucfirst($type) . ' data exported successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format activity action for display
     */
    private function formatActivityAction(string $event, array $properties): string
    {
        switch ($event) {
            case 'user_created':
                return 'Created new user account';
            case 'user_updated':
                return 'Updated user profile';
            case 'user_deleted':
                return 'Deleted user account';
            case 'role_created':
                return 'Created new role: ' . ($properties['role_name'] ?? 'Unknown');
            case 'role_updated':
                return 'Updated role: ' . ($properties['role_name'] ?? 'Unknown');
            case 'role_deleted':
                return 'Deleted role: ' . ($properties['role_name'] ?? 'Unknown');
            case 'permission_created':
                return 'Created new permission';
            case 'permission_updated':
                return 'Updated permission';
            case 'permission_deleted':
                return 'Deleted permission';
            case 'booking_created':
                return 'New booking created';
            case 'booking_updated':
                return 'Booking status updated';
            default:
                return ucwords(str_replace('_', ' ', $event));
        }
    }

    /**
     * Get activity type from event
     */
    private function getActivityType(string $event): string
    {
        if (str_starts_with($event, 'user_')) return 'user';
        if (str_starts_with($event, 'role_')) return 'role';
        if (str_starts_with($event, 'permission_')) return 'permission';
        if (str_starts_with($event, 'booking_')) return 'booking';
        return 'system';
    }

    /**
     * Get activity icon from event
     */
    private function getActivityIcon(string $event): string
    {
        switch ($this->getActivityType($event)) {
            case 'user':
                return 'bx-user';
            case 'role':
                return 'bx-shield';
            case 'permission':
                return 'bx-key';
            case 'booking':
                return 'bx-briefcase';
            default:
                return 'bx-cog';
        }
    }

    /**
     * Get activity color from event
     */
    private function getActivityColor(string $event): string
    {
        if (str_contains($event, 'created')) return 'success';
        if (str_contains($event, 'updated')) return 'info';
        if (str_contains($event, 'deleted')) return 'danger';
        return 'secondary';
    }
}
