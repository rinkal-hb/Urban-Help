<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
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
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/otp/send', [AuthController::class, 'sendOtp']);
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);
});

// Protected Authentication Routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::post('/refresh', [AuthController::class, 'refreshToken']);
    Route::post('/password/change', [AuthController::class, 'changePassword']);
    
    // Token Management
    Route::get('/tokens', [AuthController::class, 'getTokens']);
    Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken']);
    Route::delete('/tokens', [AuthController::class, 'revokeAllTokens']);
});

// User Profile Route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    $user->load('roles:id,name,display_name');
    
    return new \App\Http\Resources\UserResource($user);
});

// Role Management Routes (Admin only)
Route::middleware(['auth:sanctum', 'permission:roles.read.all'])->prefix('admin')->group(function () {
    // Dashboard routes
    Route::get('/dashboard/stats', [App\Http\Controllers\Admin\DashboardController::class, 'getStats']);
    Route::get('/dashboard/recent-users', [App\Http\Controllers\Admin\DashboardController::class, 'getRecentUsers']);
    Route::get('/dashboard/activity-logs', [App\Http\Controllers\Admin\DashboardController::class, 'getActivityLogs']);
    Route::get('/dashboard/user-growth', [App\Http\Controllers\Admin\DashboardController::class, 'getUserGrowthChart']);
    Route::get('/dashboard/role-distribution', [App\Http\Controllers\Admin\DashboardController::class, 'getRoleDistributionChart']);
    Route::get('/dashboard/export', [App\Http\Controllers\Admin\DashboardController::class, 'exportData']);

    // User management routes
    Route::apiResource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/assign-roles', [App\Http\Controllers\Admin\UserController::class, 'assignRoles'])
         ->middleware('permission:users.manage.all');
    Route::delete('/users/{user}/remove-roles', [App\Http\Controllers\Admin\UserController::class, 'removeRoles'])
         ->middleware('permission:users.manage.all');
    Route::patch('/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])
         ->middleware('permission:users.manage.all');
    Route::get('/users-stats', [App\Http\Controllers\Admin\UserController::class, 'getStats']);

    // Role management routes
    Route::apiResource('roles', RoleController::class);
    
    // Role-specific routes
    Route::prefix('roles/{role}')->group(function () {
        Route::get('/permissions', [RoleController::class, 'getPermissions']);
        Route::put('/permissions', [RoleController::class, 'syncPermissions'])
             ->middleware('permission:roles.manage.all');
        
        Route::get('/users', [RoleController::class, 'getUsers']);
        Route::post('/users', [RoleController::class, 'assignUsers'])
             ->middleware('permission:roles.manage.all');
        Route::delete('/users', [RoleController::class, 'removeUsers'])
             ->middleware('permission:roles.manage.all');
        
        Route::get('/hierarchy', [RoleController::class, 'getRoleHierarchy']);
    });
    
    // Permission management routes
    Route::apiResource('permissions', App\Http\Controllers\Admin\PermissionController::class);
    Route::get('/permissions/by-module', [App\Http\Controllers\Admin\PermissionController::class, 'getByModule']);
    Route::get('/permissions/modules', [App\Http\Controllers\Admin\PermissionController::class, 'getModules']);
    Route::get('/permissions/actions', [App\Http\Controllers\Admin\PermissionController::class, 'getActions']);
    Route::post('/permissions/bulk-create', [App\Http\Controllers\Admin\PermissionController::class, 'bulkCreate'])
         ->middleware('permission:permissions.manage.all');
    
    // Available permissions for role assignment
    Route::get('/available-permissions', [RoleController::class, 'getAvailablePermissions']);
});

// Rate Limited Routes
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/auth/login', [AuthController::class, 'apiLogin']);
    Route::post('/auth/otp/send', [AuthController::class, 'sendOtp']);
    Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp']);
});

// Strict Rate Limited Routes
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/auth/password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/password/reset', [AuthController::class, 'resetPassword']);
});
