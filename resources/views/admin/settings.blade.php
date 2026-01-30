@extends('admin.layouts.master')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Settings</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Settings</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="nav flex-column nav-pills" id="settings-tab" role="tablist">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                            <i class="ri-settings-line me-2"></i>General Settings
                        </button>
                        <button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab">
                            <i class="ri-shield-line me-2"></i>Security
                        </button>
                        <button class="nav-link" id="notifications-tab" data-bs-toggle="pill" data-bs-target="#notifications" type="button" role="tab">
                            <i class="ri-notification-line me-2"></i>Notifications
                        </button>
                        <button class="nav-link" id="appearance-tab" data-bs-toggle="pill" data-bs-target="#appearance" type="button" role="tab">
                            <i class="ri-palette-line me-2"></i>Appearance
                        </button>
                        <button class="nav-link" id="system-tab" data-bs-toggle="pill" data-bs-target="#system" type="button" role="tab">
                            <i class="ri-computer-line me-2"></i>System
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9">
            <div class="tab-content" id="settings-tabContent">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">General Settings</div>
                        </div>
                        <div class="card-body">
                            <form id="generalForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Application Name</label>
                                            <input type="text" class="form-control" id="app_name" value="Urban Help">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Application URL</label>
                                            <input type="url" class="form-control" id="app_url" value="{{ config('app.url') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Timezone</label>
                                            <select class="form-select" id="timezone">
                                                <option value="UTC">UTC</option>
                                                <option value="Asia/Kolkata" selected>Asia/Kolkata</option>
                                                <option value="America/New_York">America/New_York</option>
                                                <option value="Europe/London">Europe/London</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Default Language</label>
                                            <select class="form-select" id="language">
                                                <option value="en" selected>English</option>
                                                <option value="hi">Hindi</option>
                                                <option value="es">Spanish</option>
                                                <option value="fr">French</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Application Description</label>
                                            <textarea class="form-control" id="app_description" rows="3">Urban Help - Your trusted partner for all urban services</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Security Settings</div>
                        </div>
                        <div class="card-body">
                            <form id="securityForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Session Timeout (minutes)</label>
                                            <input type="number" class="form-control" id="session_timeout" value="120" min="5" max="1440">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Password Expiry (days)</label>
                                            <input type="number" class="form-control" id="password_expiry" value="90" min="30" max="365">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="two_factor_auth" checked>
                                                <label class="form-check-label" for="two_factor_auth">
                                                    Enable Two-Factor Authentication
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="login_attempts" checked>
                                                <label class="form-check-label" for="login_attempts">
                                                    Limit Login Attempts
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="audit_logging" checked>
                                                <label class="form-check-label" for="audit_logging">
                                                    Enable Audit Logging
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notifications Settings -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Notification Settings</div>
                        </div>
                        <div class="card-body">
                            <form id="notificationsForm">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Email Notifications</h6>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="email_new_user" checked>
                                                <label class="form-check-label" for="email_new_user">
                                                    New User Registration
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="email_new_booking" checked>
                                                <label class="form-check-label" for="email_new_booking">
                                                    New Booking Created
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="email_payment_received" checked>
                                                <label class="form-check-label" for="email_payment_received">
                                                    Payment Received
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <h6 class="mb-3">SMS Notifications</h6>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="sms_booking_updates">
                                                <label class="form-check-label" for="sms_booking_updates">
                                                    Booking Status Updates
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="sms_otp_verification" checked>
                                                <label class="form-check-label" for="sms_otp_verification">
                                                    OTP Verification
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div class="tab-pane fade" id="appearance" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Appearance Settings</div>
                        </div>
                        <div class="card-body">
                            <form id="appearanceForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Theme Mode</label>
                                            <select class="form-select" id="theme_mode">
                                                <option value="light" selected>Light</option>
                                                <option value="dark">Dark</option>
                                                <option value="auto">Auto</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Primary Color</label>
                                            <input type="color" class="form-control form-control-color" id="primary_color" value="#6c5ffc">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Sidebar Style</label>
                                            <select class="form-select" id="sidebar_style">
                                                <option value="dark" selected>Dark</option>
                                                <option value="light">Light</option>
                                                <option value="color">Color</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Layout Width</label>
                                            <select class="form-select" id="layout_width">
                                                <option value="full" selected>Full Width</option>
                                                <option value="boxed">Boxed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="tab-pane fade" id="system" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">System Information</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr><td><strong>Laravel Version:</strong></td><td>{{ app()->version() }}</td></tr>
                                        <tr><td><strong>PHP Version:</strong></td><td>{{ PHP_VERSION }}</td></tr>
                                        <tr><td><strong>Environment:</strong></td><td><span class="badge bg-{{ config('app.env') == 'production' ? 'success' : 'warning' }}">{{ config('app.env') }}</span></td></tr>
                                        <tr><td><strong>Debug Mode:</strong></td><td><span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span></td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr><td><strong>Database:</strong></td><td>{{ config('database.default') }}</td></tr>
                                        <tr><td><strong>Cache Driver:</strong></td><td>{{ config('cache.default') }}</td></tr>
                                        <tr><td><strong>Queue Driver:</strong></td><td>{{ config('queue.default') }}</td></tr>
                                        <tr><td><strong>Mail Driver:</strong></td><td>{{ config('mail.default') }}</td></tr>
                                    </table>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="mb-3">System Actions</h6>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-outline-primary" onclick="clearCache()">
                                            <i class="ri-refresh-line me-1"></i>Clear Cache
                                        </button>
                                        <button class="btn btn-outline-info" onclick="optimizeApp()">
                                            <i class="ri-speed-line me-1"></i>Optimize Application
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="backupDatabase()">
                                            <i class="ri-database-line me-1"></i>Backup Database
                                        </button>
                                        <button class="btn btn-outline-success" onclick="runMaintenance()">
                                            <i class="ri-tools-line me-1"></i>Run Maintenance
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#generalForm').on('submit', function(e) {
        e.preventDefault();
        saveGeneralSettings();
    });
    
    $('#securityForm').on('submit', function(e) {
        e.preventDefault();
        saveSecuritySettings();
    });
    
    $('#notificationsForm').on('submit', function(e) {
        e.preventDefault();
        saveNotificationSettings();
    });
    
    $('#appearanceForm').on('submit', function(e) {
        e.preventDefault();
        saveAppearanceSettings();
    });
});

function saveGeneralSettings() {
    showAlert('General settings saved successfully', 'success');
}

function saveSecuritySettings() {
    showAlert('Security settings saved successfully', 'success');
}

function saveNotificationSettings() {
    showAlert('Notification settings saved successfully', 'success');
}

function saveAppearanceSettings() {
    showAlert('Appearance settings saved successfully', 'success');
}

function clearCache() {
    showAlert('Cache cleared successfully', 'success');
}

function optimizeApp() {
    showAlert('Application optimized successfully', 'success');
}

function backupDatabase() {
    showAlert('Database backup initiated', 'info');
}

function runMaintenance() {
    showAlert('Maintenance tasks completed', 'success');
}
</script>
@endsection