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

// ADMIN PANEL ROUTES
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');

    // Permission Management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/createpermission', [PermissionController::class, 'create'])->name('permissions.createpermission');

    // Role Management
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/createrole', [RoleController::class, 'create'])->name('roles.createrole');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/data', [UserController::class, 'getData'])->name('users.data');
    Route::post('/createuser', [UserController::class, 'create'])->name('users.createuser');

    // Category Management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories/data', [CategoryController::class, 'getData'])->name('categories.data');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Service Management
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services/data', [ServiceController::class, 'data'])->name('services.data');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::get('/services-categories', [ServiceController::class, 'getCategories'])->name('services.categories');
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
