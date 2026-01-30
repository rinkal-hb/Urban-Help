<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Admin\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/otp/send', [AuthApiController::class, 'sendOtp']);
    Route::post('/otp/verify', [AuthApiController::class, 'verifyOtp']);
    Route::post('/password/forgot', [AuthApiController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthApiController::class, 'resetPassword']);
});

// Protected Authentication Routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::post('/refresh', [AuthApiController::class, 'refreshToken']);
    Route::post('/password/change', [AuthApiController::class, 'changePassword']);
    
    // Token Management
    Route::get('/tokens', [AuthApiController::class, 'getTokens']);
    Route::delete('/tokens/{tokenId}', [AuthApiController::class, 'revokeToken']);
    Route::delete('/tokens', [AuthApiController::class, 'revokeAllTokens']);
});

// User Profile Route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    $user->load('roles:id,name,display_name');
    
    return new \App\Http\Resources\UserResource($user);
});

// Admin Routes (Protected)
Route::middleware(['auth:sanctum', 'permission:dashboard.read.all'])->prefix('admin')->group(function () {
    // Dashboard routes
    Route::get('/dashboard/stats', [App\Http\Controllers\Admin\DashboardController::class, 'getStats']);
    Route::get('/dashboard/recent-users', [App\Http\Controllers\Admin\DashboardController::class, 'getRecentUsers']);
    Route::get('/dashboard/activity-logs', [App\Http\Controllers\Admin\DashboardController::class, 'getActivityLogs']);
    Route::get('/dashboard/user-growth', [App\Http\Controllers\Admin\DashboardController::class, 'getUserGrowthChart']);
    Route::get('/dashboard/role-distribution', [App\Http\Controllers\Admin\DashboardController::class, 'getRoleDistributionChart']);
    Route::get('/dashboard/export', [App\Http\Controllers\Admin\DashboardController::class, 'exportData']);

    // User management routes
    Route::middleware('permission:users.read.all')->group(function () {
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'getData']);
        Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::get('/users/stats', [App\Http\Controllers\Admin\UserController::class, 'getStats']);
    });
    
    Route::middleware('permission:users.manage.all')->group(function () {
        Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);
        Route::patch('/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus']);
        Route::post('/users/bulk-assign-role', [App\Http\Controllers\Admin\UserController::class, 'bulkAssignRole']);
    });

    // Role management routes
    Route::middleware('permission:roles.read.all')->group(function () {
        Route::get('/roles', [RoleController::class, 'getData']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
        Route::get('/roles/available-permissions', [RoleController::class, 'getAvailablePermissions']);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'getPermissions']);
        Route::get('/roles/{role}/users', [RoleController::class, 'getUsers']);
    });
    
    Route::middleware('permission:roles.manage.all')->group(function () {
        Route::post('/roles', [RoleController::class, 'store']);
        Route::put('/roles/{role}', [RoleController::class, 'update']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
        Route::put('/roles/{role}/permissions', [RoleController::class, 'syncPermissions']);
        Route::post('/roles/{role}/users', [RoleController::class, 'assignUsers']);
        Route::delete('/roles/{role}/users', [RoleController::class, 'removeUsers']);
    });

    // Permission management routes
    Route::middleware('permission:permissions.read.all')->group(function () {
        Route::get('/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'getData']);
        Route::get('/permissions/{permission}', [App\Http\Controllers\Admin\PermissionController::class, 'show']);
        Route::get('/permissions/stats', [App\Http\Controllers\Admin\PermissionController::class, 'getStats']);
        Route::get('/permissions/by-module/{module}', [App\Http\Controllers\Admin\PermissionController::class, 'getByModule']);
    });
    
    Route::middleware('permission:permissions.manage.all')->group(function () {
        Route::post('/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'store']);
        Route::put('/permissions/{permission}', [App\Http\Controllers\Admin\PermissionController::class, 'update']);
        Route::delete('/permissions/{permission}', [App\Http\Controllers\Admin\PermissionController::class, 'destroy']);
        Route::post('/permissions/bulk-create', [App\Http\Controllers\Admin\PermissionController::class, 'bulkCreate']);
    });

    // Category management routes
    // Route::apiResource('categories', App\Http\Controllers\Admin\CategoryController::class);
    // Route::get('/categories-data', [App\Http\Controllers\Admin\CategoryController::class, 'getData']);

    // Service management routes
    // Route::apiResource('services', App\Http\Controllers\Admin\ServiceController::class);
    // Route::get('/services-data', [App\Http\Controllers\Admin\ServiceController::class, 'getData']);

    // Booking management routes
    // Route::apiResource('bookings', App\Http\Controllers\Admin\BookingController::class);
    // Route::get('/bookings-data', [App\Http\Controllers\Admin\BookingController::class, 'getData']);
    // Route::post('/bookings/{booking}/assign-provider', [App\Http\Controllers\Admin\BookingController::class, 'assignProvider'])
    //      ->middleware('permission:providers.assign.all');

    // Payment management routes
    // Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])
    //      ->middleware('permission:payments.read.all');
    // Route::get('/payments-data', [App\Http\Controllers\Admin\PaymentController::class, 'getData'])
    //      ->middleware('permission:payments.read.all');

    // Provider management routes
    // Route::get('/providers', [App\Http\Controllers\Admin\ProviderController::class, 'index'])
    //      ->middleware('permission:providers.read.all');
    // Route::get('/providers-data', [App\Http\Controllers\Admin\ProviderController::class, 'getData'])
    //      ->middleware('permission:providers.read.all');

    // Customer management routes
    // Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])
    //      ->middleware('permission:customers.read.all');
    // Route::get('/customers-data', [App\Http\Controllers\Admin\CustomerController::class, 'getData'])
    //      ->middleware('permission:customers.read.all');

    // Reports routes
    // Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])
    //      ->middleware('permission:reports.read.all');
    // Route::get('/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])
    //      ->middleware('permission:reports.export.all');

    // Audit logs routes
    // Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])
    //      ->middleware('permission:audit.read.all');
    // Route::get('/audit-logs-data', [App\Http\Controllers\Admin\AuditLogController::class, 'getData'])
    //      ->middleware('permission:audit.read.all');
});
