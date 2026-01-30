<?php

require('temporary.php');


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;


// AUTHENTICATION ROUTES
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home.login');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('logout', [AuthController::class, 'logout']);

// ADMIN PANEL ROUTES
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Route
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/profile', 'profile')->name('profile');
        Route::post('/profile/update', 'updateProfile')->name('profile.update');
        Route::post('/profile/change-password', 'changePassword')->name('profile.change-password');
    });

    // Permission Management
    Route::controller(PermissionController::class)->group(function () {
        Route::get('/permissions', 'index')->name('permissions.index');
        Route::get('/permissions/data', 'getData')->name('permissions.data');
        Route::get('/permissions/{permission}', 'show')->name('permissions.show');
        Route::post('/permissions', 'store')->name('permissions.createpermission');
        Route::match(['POST', 'PUT'], '/permissions/{permission}', 'update')->name('permissions.update');
        Route::delete('/permissions/{permission}', 'destroy')->name('permissions.destroy');
    });

    // Role Management
    Route::controller(RoleController::class)->group(function () {
        Route::get('/roles', 'index')->name('roles.index');
        Route::get('/roles/data', 'getData')->name('roles.data');
        Route::get('/roles/{role}', 'show')->name('roles.show');
        Route::get('/roles/{role}/permissions', 'getPermissions')->name('roles.permissions');
        Route::get('/roles/{role}/users', 'getUsers')->name('roles.users');
        Route::get('/permissions/available', 'getAvailablePermissions')->name('permissions.available');
        Route::post('/roles', 'store')->name('roles.store');
        Route::match(['POST', 'PUT'], '/roles/{role}', 'update')->name('roles.update');
        Route::delete('/roles/{role}', 'destroy')->name('roles.destroy');
        Route::post('/roles/{role}/permissions', 'syncPermissions')->name('roles.sync-permissions');
        Route::post('/roles/{role}/assign-users', 'assignUsers')->name('roles.assign-users');
        Route::post('/roles/{role}/remove-users', 'removeUsers')->name('roles.remove-users');
    });

    // User Management
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::get('/users/data', 'getData')->name('users.data');
        Route::get('/users/{user}', 'show')->name('users.show');
        Route::get('/users/stats', 'getStats')->name('users.stats');
        Route::post('/users', 'store')->name('users.store');
        Route::match(['POST', 'PUT'], '/users/{user}', 'update')->name('users.update');
        Route::delete('/users/{user}', 'destroy')->name('users.destroy');
        Route::post('/users/{user}/toggle-status', 'toggleStatus')->name('users.toggle-status');
        Route::post('/users/bulk-assign-role', 'bulkAssignRole')->name('users.bulk-assign-role');
    });

    // Categories
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index')->name('categories.index');
        Route::post('/categories/data', 'getData')->name('categories.data');
        Route::get('/categories/{category}', 'show')->name('categories.show');
        Route::post('/categories', 'store')->name('categories.store');
        Route::post('/categories/{category}', 'update')->name('categories.update');
        Route::delete('/categories/{category}', 'destroy')->name('categories.destroy');
    });

    // Services
    Route::controller(ServiceController::class)->group(function () {
        Route::get('/services', 'index')->name('services.index');
        Route::post('/services/data', 'data')->name('services.data');
        Route::get('/services/{service}', 'show')->name('services.show');
        Route::get('/services-categories', 'getCategories')->name('services.categories');
        Route::post('/services', 'store')->name('services.store');
        Route::match(['POST', 'PUT'], '/services/{service}', 'update')->name('services.update');
        Route::delete('/services/{service}', 'destroy')->name('services.destroy');
        Route::post('/services/{service}/toggle-status', 'toggleStatus')->name('services.toggle-status');
    });

    // Profile and Settings Routes


    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    // Other modules
    Route::get('/bookings', function () {
        return view('admin.bookings.index');
    })->name('bookings.index');
    Route::get('/providers', function () {
        return view('admin.providers.index');
    })->name('providers.index');
    Route::get('/customers', function () {
        return view('admin.customers.index');
    })->name('customers.index');
    Route::get('/payments', function () {
        return view('admin.payments.index');
    })->name('payments.index');
    Route::get('/reports', function () {
        return view('admin.reports.index');
    })->name('reports.index');
    Route::get('/audit', function () {
        return view('admin.audit.index');
    })->name('audit.index');
});

// CUSTOMER ROUTES
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');
});

// PROVIDER ROUTES
Route::middleware(['auth', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/dashboard', function () {
        return view('provider.dashboard');
    })->name('dashboard');
});
