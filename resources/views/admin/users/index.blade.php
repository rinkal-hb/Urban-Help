@extends('admin.layouts.master')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">User Management</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary">
                                <i class="bx bx-user fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Users</p>
                                    <h4 class="fw-semibold mt-1" id="totalUsers">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-success">
                                <i class="bx bx-check-circle fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Active Users</p>
                                    <h4 class="fw-semibold mt-1" id="activeUsers">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-warning">
                                <i class="bx bx-user-plus fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">New This Month</p>
                                    <h4 class="fw-semibold mt-1" id="newUsers">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-info">
                                <i class="bx bx-shield fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Admin Users</p>
                                    <h4 class="fw-semibold mt-1" id="adminUsers">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Management Card -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        <i class="bx bx-user me-2"></i>Users Management
                    </div>
                    <div class="d-flex">
                        @can('users.manage.all')
                        <button class="btn btn-sm btn-primary me-2" onclick="openCreateUserModal()">
                            <i class="ri-add-line me-1"></i>Create User
                        </button>
                        <button class="btn btn-sm btn-success me-2" onclick="openBulkImportModal()">
                            <i class="ri-upload-line me-1"></i>Import Users
                        </button>
                        @endcan
                        <button class="btn btn-sm btn-info me-2" onclick="exportUsers()">
                            <i class="ri-download-line me-1"></i>Export
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="refreshUsers()">
                            <i class="ri-refresh-line me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Search Users</label>
                            <input type="text" class="form-control" id="searchUsers" placeholder="Search by name, email, phone...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Role</label>
                            <select class="form-select" id="filterRole">
                                <option value="">All Roles</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="filterStatus">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Verification</label>
                            <select class="form-select" id="filterVerification">
                                <option value="">All</option>
                                <option value="email_verified">Email Verified</option>
                                <option value="phone_verified">Phone Verified</option>
                                <option value="identity_verified">Identity Verified</option>
                                <option value="unverified">Unverified</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">View</label>
                            <select class="form-select" id="viewMode">
                                <option value="table">Table View</option>
                                <option value="cards">Card View</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                                <i class="ri-filter-line"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div id="tableView">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-hover" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>User</th>
                                        <th>Contact</th>
                                        <th>Roles</th>
                                        <th>Status</th>
                                        <th>Verification</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card View -->
                    <div id="cardView" style="display: none;">
                        <div class="row" id="usersCards">
                            <!-- Dynamic cards -->
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div id="bulkActions" style="display: none;">
                            @can('users.manage.all')
                            <button class="btn btn-sm btn-success" onclick="bulkActivate()">
                                <i class="ri-check-line me-1"></i>Activate Selected
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="bulkDeactivate()">
                                <i class="ri-close-line me-1"></i>Deactivate Selected
                            </button>
                            <button class="btn btn-sm btn-info" onclick="bulkAssignRole()">
                                <i class="ri-shield-line me-1"></i>Assign Role
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="bulkDelete()">
                                <i class="ri-delete-line me-1"></i>Delete Selected
                            </button>
                            @endcan
                        </div>
                        <div id="usersInfo"></div>
                        <nav id="usersPagination"></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Create User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId">
                    
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Basic Information</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="userName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="userEmail" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="userPhone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger" id="passwordRequired">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="userPassword">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="ri-eye-line" id="passwordToggleIcon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Leave blank to keep current password (for edit)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Role & Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Role & Status</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Primary Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="userRole" required>
                                    <option value="">Select Role</option>
                                    <option value="customer">Customer</option>
                                    <option value="provider">Service Provider</option>
                                    <option value="admin">Administrator</option>
                                    <option value="super_admin">Super Administrator</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="userStatus">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Additional Roles</label>
                                <div id="additionalRolesContainer" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                    <!-- Dynamic roles will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Personal Information</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="userDateOfBirth">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select class="form-select" id="userGender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Avatar</label>
                                <input type="file" class="form-control" id="userAvatar" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Address Information</h6>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" id="userAddress" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" id="userCity">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" id="userState">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="userPostalCode">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" id="userCountry" value="India">
                            </div>
                        </div>
                    </div>

                    <!-- Provider Specific (Show only for providers) -->
                    <div class="row mb-4" id="providerSection" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Provider Information</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Provider Type</label>
                                <select class="form-select" id="userProviderType">
                                    <option value="">Select Type</option>
                                    <option value="individual">Individual</option>
                                    <option value="company">Company</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Experience (Years)</label>
                                <input type="number" class="form-control" id="userExperience" min="0" max="50">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Hourly Rate</label>
                                <input type="number" class="form-control" id="userHourlyRate" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Business Name</label>
                                <input type="text" class="form-control" id="userBusinessName">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Business License</label>
                                <input type="text" class="form-control" id="userBusinessLicense">
                            </div>
                        </div>
                    </div>

                    <!-- Verification Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Verification Status</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="emailVerified">
                                <label class="form-check-label" for="emailVerified">
                                    Email Verified
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="phoneVerified">
                                <label class="form-check-label" for="phoneVerified">
                                    Phone Verified
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="identityVerified">
                                <label class="form-check-label" for="identityVerified">
                                    Identity Verified
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveUserBtn">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsTitle">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="userDetailsContent">
                    <!-- Dynamic content -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Role Assignment Modal -->
<div class="modal fade" id="bulkRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Role to Selected Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Role</label>
                    <select class="form-select" id="bulkRoleSelect">
                        <option value="">Select Role</option>
                    </select>
                </div>
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    This will assign the selected role to <strong id="selectedUsersCount">0</strong> users.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmBulkRoleAssignment()">Assign Role</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1;
let currentFilters = {};
let selectedUsers = [];
let allRoles = [];

$(document).ready(function() {
    loadUsers();
    loadRoles();
    loadStats();
    
    // Search functionality
    $('#searchUsers').on('keyup', debounce(function() {
        applyFilters();
    }, 500));
    
    // View mode change
    $('#viewMode').on('change', function() {
        switchView($(this).val());
    });
    
    // Form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        saveUser();
    });
    
    // Role change handler
    $('#userRole').on('change', function() {
        toggleProviderSection();
    });
    
    // Select all functionality
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.user-checkbox').prop('checked', isChecked);
        updateBulkActions();
    });
    
    // Individual checkbox change
    $(document).on('change', '.user-checkbox', function() {
        updateBulkActions();
    });
});

function loadUsers(page = 1) {
    const params = new URLSearchParams({
        page: page,
        per_page: 15,
        ...currentFilters
    });
    
    showLoading();
    
    $.ajax({
        url: `/api/admin/users?${params}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                const viewMode = $('#viewMode').val();
                if (viewMode === 'table') {
                    renderUsersTable(response.data.users);
                } else {
                    renderUsersCards(response.data.users);
                }
                renderPagination(response.data.pagination);
            }
        },
        error: function(xhr) {
            showAlert('Error loading users', 'error');
        },
        complete: function() {
            hideLoading();
        }
    });
}

function renderUsersTable(users) {
    const tbody = $('#usersTableBody');
    tbody.empty();
    
    users.forEach(user => {
        const avatar = user.avatar 
            ? `<img src="${user.avatar}" class="avatar avatar-sm rounded-circle me-2" alt="${user.name}">`
            : `<span class="avatar avatar-sm rounded-circle bg-primary me-2">${user.name.charAt(0)}</span>`;
            
        const statusBadge = user.is_active 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
            
        const roles = user.roles && user.roles.length > 0
            ? user.roles.map(role => `<span class="badge bg-primary me-1">${role.display_name}</span>`).join('')
            : '<span class="badge bg-secondary">No roles</span>';
            
        const verificationBadges = [];
        if (user.email_verified_at) verificationBadges.push('<span class="badge bg-success">Email</span>');
        if (user.phone_verified_at) verificationBadges.push('<span class="badge bg-info">Phone</span>');
        if (user.identity_verified_at) verificationBadges.push('<span class="badge bg-warning">Identity</span>');
        
        const verification = verificationBadges.length > 0 
            ? verificationBadges.join(' ')
            : '<span class="badge bg-secondary">Unverified</span>';
            
        const lastLogin = user.last_login_at 
            ? new Date(user.last_login_at).toLocaleDateString()
            : 'Never';
            
        tbody.append(`
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input user-checkbox" value="${user.id}">
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        ${avatar}
                        <div>
                            <strong>${user.name}</strong>
                            <br><small class="text-muted">${user.role || 'No role'}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div>
                        <i class="ri-mail-line me-1"></i>${user.email}
                        <br><i class="ri-phone-line me-1"></i>${user.phone || 'N/A'}
                    </div>
                </td>
                <td>${roles}</td>
                <td>${statusBadge}</td>
                <td>${verification}</td>
                <td>${lastLogin}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info" onclick="viewUser(${user.id})" title="View Details">
                            <i class="ri-eye-line"></i>
                        </button>
                        @can('users.manage.all')
                        <button class="btn btn-sm btn-primary" onclick="editUser(${user.id})" title="Edit">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="toggleUserStatus(${user.id}, ${user.is_active ? 0 : 1})" title="${user.is_active ? 'Deactivate' : 'Activate'}">
                            <i class="ri-${user.is_active ? 'close' : 'check'}-line"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id}, '${user.name}')" title="Delete">
                            <i class="ri-delete-line"></i>
                        </button>
                        @endcan
                    </div>
                </td>
            </tr>
        `);
    });
}

function renderUsersCards(users) {
    const container = $('#usersCards');
    container.empty();
    
    users.forEach(user => {
        const avatar = user.avatar 
            ? `<img src="${user.avatar}" class="avatar avatar-lg rounded-circle" alt="${user.name}">`
            : `<span class="avatar avatar-lg rounded-circle bg-primary">${user.name.charAt(0)}</span>`;
            
        const statusBadge = user.is_active 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
            
        const roles = user.roles && user.roles.length > 0
            ? user.roles.map(role => `<span class="badge bg-primary me-1">${role.display_name}</span>`).join('')
            : '<span class="badge bg-secondary">No roles</span>';
            
        container.append(`
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="card custom-card card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                ${avatar}
                                <div class="ms-3">
                                    <h6 class="card-title mb-1">${user.name}</h6>
                                    <small class="text-muted">${user.role || 'No role'}</small>
                                </div>
                            </div>
                            <input type="checkbox" class="form-check-input user-checkbox" value="${user.id}">
                        </div>
                        
                        <div class="mb-2">
                            <i class="ri-mail-line me-1"></i>${user.email}
                            <br><i class="ri-phone-line me-1"></i>${user.phone || 'N/A'}
                        </div>
                        
                        <div class="mb-3">
                            ${roles}
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            ${statusBadge}
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info" onclick="viewUser(${user.id})">
                                    <i class="ri-eye-line"></i>
                                </button>
                                @can('users.manage.all')
                                <button class="btn btn-sm btn-primary" onclick="editUser(${user.id})">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="toggleUserStatus(${user.id}, ${user.is_active ? 0 : 1})">
                                    <i class="ri-${user.is_active ? 'close' : 'check'}-line"></i>
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    });
}

function switchView(viewMode) {
    $('#tableView, #cardView').hide();
    $(`#${viewMode}View`).show();
    loadUsers(currentPage);
}

function openCreateUserModal() {
    $('#userModalTitle').text('Create User');
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#passwordRequired').show();
    $('#userPassword').prop('required', true);
    loadRolesForUser();
    toggleProviderSection();
    $('#userModal').modal('show');
}

function editUser(userId) {
    $.ajax({
        url: `/api/admin/users/${userId}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                const user = response.data.user;
                
                $('#userModalTitle').text('Edit User');
                $('#userId').val(user.id);
                $('#passwordRequired').hide();
                $('#userPassword').prop('required', false);
                
                // Fill form fields
                $('#userName').val(user.name);
                $('#userEmail').val(user.email);
                $('#userPhone').val(user.phone);
                $('#userRole').val(user.role);
                $('#userStatus').val(user.is_active ? '1' : '0');
                $('#userDateOfBirth').val(user.date_of_birth);
                $('#userGender').val(user.gender);
                $('#userAddress').val(user.address);
                $('#userCity').val(user.city);
                $('#userState').val(user.state);
                $('#userPostalCode').val(user.postal_code);
                $('#userCountry').val(user.country);
                $('#userProviderType').val(user.provider_type);
                $('#userExperience').val(user.experience_years);
                $('#userHourlyRate').val(user.hourly_rate);
                $('#userBusinessName').val(user.business_name);
                $('#userBusinessLicense').val(user.business_license);
                
                // Set verification checkboxes
                $('#emailVerified').prop('checked', !!user.email_verified_at);
                $('#phoneVerified').prop('checked', !!user.phone_verified_at);
                $('#identityVerified').prop('checked', !!user.identity_verified_at);
                
                loadRolesForUser(user.roles);
                toggleProviderSection();
                $('#userModal').modal('show');
            }
        },
        error: function(xhr) {
            showAlert('Error loading user details', 'error');
        }
    });
}

function saveUser() {
    const userId = $('#userId').val();
    const isEdit = userId !== '';
    
    const formData = new FormData();
    
    // Basic information
    formData.append('name', $('#userName').val());
    formData.append('email', $('#userEmail').val());
    formData.append('phone', $('#userPhone').val());
    formData.append('role', $('#userRole').val());
    formData.append('is_active', $('#userStatus').val());
    
    // Password (only if provided)
    const password = $('#userPassword').val();
    if (password) {
        formData.append('password', password);
    }
    
    // Personal information
    if ($('#userDateOfBirth').val()) formData.append('date_of_birth', $('#userDateOfBirth').val());
    if ($('#userGender').val()) formData.append('gender', $('#userGender').val());
    if ($('#userAddress').val()) formData.append('address', $('#userAddress').val());
    if ($('#userCity').val()) formData.append('city', $('#userCity').val());
    if ($('#userState').val()) formData.append('state', $('#userState').val());
    if ($('#userPostalCode').val()) formData.append('postal_code', $('#userPostalCode').val());
    if ($('#userCountry').val()) formData.append('country', $('#userCountry').val());
    
    // Provider information
    if ($('#userProviderType').val()) formData.append('provider_type', $('#userProviderType').val());
    if ($('#userExperience').val()) formData.append('experience_years', $('#userExperience').val());
    if ($('#userHourlyRate').val()) formData.append('hourly_rate', $('#userHourlyRate').val());
    if ($('#userBusinessName').val()) formData.append('business_name', $('#userBusinessName').val());
    if ($('#userBusinessLicense').val()) formData.append('business_license', $('#userBusinessLicense').val());
    
    // Verification status
    formData.append('email_verified', $('#emailVerified').is(':checked') ? '1' : '0');
    formData.append('phone_verified', $('#phoneVerified').is(':checked') ? '1' : '0');
    formData.append('identity_verified', $('#identityVerified').is(':checked') ? '1' : '0');
    
    // Avatar file
    const avatarFile = $('#userAvatar')[0].files[0];
    if (avatarFile) {
        formData.append('avatar', avatarFile);
    }
    
    // Additional roles
    const additionalRoles = [];
    $('input[name="additional_roles[]"]:checked').each(function() {
        additionalRoles.push(parseInt($(this).val()));
    });
    formData.append('additional_roles', JSON.stringify(additionalRoles));
    
    const url = isEdit ? `/api/admin/users/${userId}` : '/api/admin/users';
    const method = isEdit ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#userModal').modal('hide');
                showAlert(response.message, 'success');
                loadUsers(currentPage);
                loadStats();
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                let errorMessage = 'Validation errors:\n';
                Object.keys(response.errors).forEach(field => {
                    errorMessage += `${field}: ${response.errors[field].join(', ')}\n`;
                });
                showAlert(errorMessage, 'error');
            } else {
                showAlert('Error saving user', 'error');
            }
        }
    });
}

function toggleProviderSection() {
    const role = $('#userRole').val();
    if (role === 'provider') {
        $('#providerSection').show();
    } else {
        $('#providerSection').hide();
    }
}

function togglePassword() {
    const passwordField = $('#userPassword');
    const toggleIcon = $('#passwordToggleIcon');
    
    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        toggleIcon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
    } else {
        passwordField.attr('type', 'password');
        toggleIcon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
    }
}

function loadRoles() {
    $.ajax({
        url: '/api/admin/roles',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                allRoles = response.data.roles;
                
                // Update filter dropdown
                const roleFilter = $('#filterRole');
                roleFilter.find('option:not(:first)').remove();
                allRoles.forEach(role => {
                    roleFilter.append(`<option value="${role.name}">${role.display_name}</option>`);
                });
                
                // Update bulk role dropdown
                const bulkRoleSelect = $('#bulkRoleSelect');
                bulkRoleSelect.find('option:not(:first)').remove();
                allRoles.forEach(role => {
                    bulkRoleSelect.append(`<option value="${role.name}">${role.display_name}</option>`);
                });
            }
        }
    });
}

function loadRolesForUser(selectedRoles = []) {
    const container = $('#additionalRolesContainer');
    container.empty();
    
    const selectedRoleIds = selectedRoles.map(role => role.id);
    
    allRoles.forEach(role => {
        const isChecked = selectedRoleIds.includes(role.id) ? 'checked' : '';
        container.append(`
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" 
                       id="additional_role_${role.id}" 
                       name="additional_roles[]" 
                       value="${role.id}" ${isChecked}>
                <label class="form-check-label" for="additional_role_${role.id}">
                    ${role.display_name}
                    <small class="text-muted d-block">${role.description}</small>
                </label>
            </div>
        `);
    });
}

function loadStats() {
    $.ajax({
        url: '/api/admin/users/stats',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                $('#totalUsers').text(stats.total_users || 0);
                $('#activeUsers').text(stats.active_users || 0);
                $('#newUsers').text(stats.new_users || 0);
                $('#adminUsers').text(stats.admin_users || 0);
            }
        }
    });
}

function applyFilters() {
    currentFilters = {
        search: $('#searchUsers').val(),
        role: $('#filterRole').val(),
        status: $('#filterStatus').val(),
        verification: $('#filterVerification').val()
    };
    
    // Remove empty filters
    Object.keys(currentFilters).forEach(key => {
        if (!currentFilters[key]) {
            delete currentFilters[key];
        }
    });
    
    currentPage = 1;
    loadUsers(currentPage);
}

function refreshUsers() {
    currentFilters = {};
    $('#searchUsers').val('');
    $('#filterRole').val('');
    $('#filterStatus').val('');
    $('#filterVerification').val('');
    loadUsers();
    loadStats();
}

function updateBulkActions() {
    const checkedBoxes = $('.user-checkbox:checked');
    selectedUsers = checkedBoxes.map(function() {
        return parseInt($(this).val());
    }).get();
    
    if (selectedUsers.length > 0) {
        $('#bulkActions').show();
        $('#selectedUsersCount').text(selectedUsers.length);
    } else {
        $('#bulkActions').hide();
    }
}

function bulkAssignRole() {
    if (selectedUsers.length === 0) {
        showAlert('Please select users first', 'warning');
        return;
    }
    $('#bulkRoleModal').modal('show');
}

function confirmBulkRoleAssignment() {
    const roleId = $('#bulkRoleSelect').val();
    if (!roleId) {
        showAlert('Please select a role', 'warning');
        return;
    }
    
    $.ajax({
        url: '/api/admin/users/bulk-assign-role',
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            user_ids: selectedUsers,
            role: roleId
        }),
        success: function(response) {
            if (response.success) {
                $('#bulkRoleModal').modal('hide');
                showAlert(response.message, 'success');
                loadUsers(currentPage);
                $('.user-checkbox').prop('checked', false);
                $('#selectAll').prop('checked', false);
                updateBulkActions();
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert(response?.message || 'Error assigning roles', 'error');
        }
    });
}

function renderPagination(pagination) {
    const info = `Showing ${((pagination.current_page - 1) * pagination.per_page) + 1} to ${Math.min(pagination.current_page * pagination.per_page, pagination.total)} of ${pagination.total} users`;
    $('#usersInfo').text(info);
    
    let paginationHtml = '<ul class="pagination mb-0">';
    
    if (pagination.current_page > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadUsers(${pagination.current_page - 1})">Previous</a></li>`;
    }
    
    for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
        const active = i === pagination.current_page ? 'active' : '';
        paginationHtml += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadUsers(${i})">${i}</a></li>`;
    }
    
    if (pagination.current_page < pagination.last_page) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadUsers(${pagination.current_page + 1})">Next</a></li>`;
    }
    
    paginationHtml += '</ul>';
    $('#usersPagination').html(paginationHtml);
}

// Utility functions
function getAuthToken() {
    return localStorage.getItem('auth_token') || '';
}

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : type === 'warning' ? 'alert-warning' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container-fluid').prepend(alertHtml);
    
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

function showLoading() {
    // Add loading spinner
}

function hideLoading() {
    // Remove loading spinner
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endsection

@section('styles')
<style>
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
}

.user-checkbox {
    cursor: pointer;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
}

.modal-xl {
    max-width: 1200px;
}

.form-check-input:checked {
    background-color: #6c5ffc;
    border-color: #6c5ffc;
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endsection