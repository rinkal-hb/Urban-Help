<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Service;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,super_admin');
    }

    /**
     * Show admin dashboard
     */
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }

    /**
     * Get dashboard data dynamically
     */
    public function getDashboardData(): JsonResponse
    {
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'total_providers' => User::where('role', 'provider')->count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
        ];

        $recent_bookings = Booking::with(['customer', 'service', 'provider'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_bookings' => $recent_bookings
            ]
        ]);
    }

    /**
     * Show admin dashboard (alternative route)
     */
    public function index(): View
    {
        return $this->dashboard();
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            // User Statistics
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'customers' => User::where('role', 'customer')->count(),
                'providers' => User::where('role', 'provider')->count(),
                'admins' => User::where('role', 'admin')->count(),
                'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            ],

            // Booking Statistics
            'bookings' => [
                'total' => Booking::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'completed' => Booking::where('status', 'completed')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
                'today' => Booking::whereDate('created_at', today())->count(),
            ],

            // Service Statistics
            'services' => [
                'total' => Service::count(),
                'active' => Service::count(), // Assuming all services are active
                'categories' => Category::count(),
            ],

            // Revenue Statistics
            'revenue' => [
                'total' => Payment::where('status', 'completed')->sum('amount'),
                'this_month' => Payment::where('status', 'completed')
                                      ->where('created_at', '>=', now()->startOfMonth())
                                      ->sum('amount'),
                'today' => Payment::where('status', 'completed')
                                 ->whereDate('created_at', today())
                                 ->sum('amount'),
            ],

            // Role & Permission Statistics
            'roles_permissions' => [
                'total_roles' => Role::count(),
                'active_roles' => Role::where('is_active', true)->count(),
                'total_permissions' => Permission::count(),
                'permissions_by_module' => Permission::selectRaw('module, COUNT(*) as count')
                                                   ->groupBy('module')
                                                   ->pluck('count', 'module'),
            ],

            // Recent Activity
            'recent_activity' => $this->getRecentActivity(),

            // System Health
            'system_health' => [
                'locked_users' => User::whereNotNull('locked_until')
                                     ->where('locked_until', '>', now())
                                     ->count(),
                'unverified_users' => User::whereNull('email_verified_at')
                                         ->orWhereNull('phone_verified_at')
                                         ->count(),
                'failed_logins_today' => AuditLog::where('event_type', 'failed_login_attempt')
                                                 ->where('created_at', '>=', now()->startOfDay())
                                                 ->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get recent user registrations
     */
    public function getRecentUsers(): JsonResponse
    {
        $users = User::with('roles:id,name,display_name')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'recent_users' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'roles' => $user->roles->pluck('display_name'),
                        'is_active' => $user->is_active,
                        'created_at' => $user->created_at,
                        'city' => $user->city,
                    ];
                })
            ]
        ]);
    }

    /**
     * Get system activity logs
     */
    public function getActivityLogs(Request $request): JsonResponse
    {
        $query = AuditLog::with('user:id,name,email')
                         ->orderBy('created_at', 'desc');

        // Filter by event type
        if ($request->has('event_type')) {
            $query->where('event_type', $request->string('event_type'));
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->date('start_date'));
        }

        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->date('end_date'));
        }

        $logs = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total()
                ]
            ]
        ]);
    }

    /**
     * Get user growth chart data
     */
    public function getUserGrowthChart(): JsonResponse
    {
        $months = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $count = User::where('created_at', '<=', $date->endOfMonth())
                        ->count();
            $data[] = $count;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $months,
                'data' => $data
            ]
        ]);
    }

    /**
     * Get role distribution chart data
     */
    public function getRoleDistributionChart(): JsonResponse
    {
        $roleData = User::selectRaw('role, COUNT(*) as count')
                       ->groupBy('role')
                       ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $roleData->pluck('role'),
                'data' => $roleData->pluck('count')
            ]
        ]);
    }

    /**
     * Get recent activity
     */
    protected function getRecentActivity(): array
    {
        $recentLogs = AuditLog::with('user:id,name')
                             ->whereIn('event_type', [
                                 'successful_login', 'web_logout', 'role_assigned', 'role_removed',
                                 'user_created', 'user_updated', 'permission_granted'
                             ])
                             ->orderBy('created_at', 'desc')
                             ->limit(10)
                             ->get();

        return $recentLogs->map(function ($log) {
            return [
                'id' => $log->id,
                'event' => $log->event_description,
                'user' => $log->user?->name ?? 'System',
                'created_at' => $log->created_at,
                'ip_address' => $log->ip_address,
            ];
        })->toArray();
    }

    /**
     * Export system data
     */
    public function exportData(Request $request): JsonResponse
    {
        $type = $request->string('type', 'users');

        try {
            switch ($type) {
                case 'users':
                    $data = User::with('roles')->get();
                    break;
                case 'roles':
                    $data = Role::with('permissions')->get();
                    break;
                case 'permissions':
                    $data = Permission::all();
                    break;
                case 'audit_logs':
                    $data = AuditLog::with('user')->limit(1000)->get();
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid export type'
                    ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'exported_at' => now(),
                'count' => $data->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}