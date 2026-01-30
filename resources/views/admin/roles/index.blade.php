@extends('admin.layouts.master')

@section('title', 'Role Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Role Management</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Roles</li>
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
                                <i class="bx bx-shield fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Roles</p>
                                    <h4 class="fw-semibold mt-1" id="totalRoles">0</h4>
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
                                    <p class="text-muted mb-0">Active Roles</p>
                                    <h4 class="fw-semibold mt-1" id="activeRoles">0</h4>
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
                                <i class="bx bx-user fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Users with Roles</p>
                                    <h4 class="fw-semibold mt-1" id="usersWithRoles">0</h4>
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
                                <i class="bx bx-key fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Permissions</p>
                                    <h4 class="fw-semibold mt-1" id="totalPermissions">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Hierarchy Visualization -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-sitemap me-2"></i>Role Hierarchy
                    </div>
                </div>
                <div class="card-body">
                    <div id="roleHierarchy" class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                        <!-- Dynamic hierarchy visualization -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Management Card -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        <i class="bx bx-shield me-2"></i>Roles & Permissions
                    </div>
                    <div class="d-flex">
                        @can('roles.manage.all')
                        <button class="btn btn-sm btn-primary me-2" onclick="openCreateRoleModal()">
                            <i class="ri-add-line me-1"></i>Create Role
                        </button>
                        @endcan
                        <button class="btn btn-sm btn-info me-2" onclick="openPermissionMatrixModal()">
                            <i class="ri-table-line me-1"></i>Permission Matrix
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="refreshRoles()">
                            <i class="ri-refresh-line me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchRoles" placeholder="Search roles...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterActive">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterHierarchy">
                                <option value="">All Levels</option>
                                <option value="90">Admin Level (90+)</option>
                                <option value="70">Manager Level (70+)</option>
                                <option value="50">Provider Level (50+)</option>
                                <option value="10">Customer Level (10+)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                                <i class="ri-filter-line me-1"></i>Filter
                            </button>
                        </div>
                    </div>

                    <!-- Roles Table -->
                    <div class="table-responsive">
                        <table class="table text-nowrap table-hover" id="rolesTable">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Hierarchy Level</th>
                                    <th>Permissions</th>
                                    <th>Users</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="rolesTableBody">
                                <!-- Dynamic content -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div id="rolesInfo"></div>
                        <nav id="rolesPagination"></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Create Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm">
                <div class="modal-body">
                    <input type="hidden" id="roleId">
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Basic Information</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="roleName" required>
                                <small class="text-muted">Use lowercase with underscores (e.g., service_manager)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="roleDisplayName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hierarchy Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="roleHierarchy" min="0" max="99" required>
                                <small class="text-muted">Higher numbers have more authority (0-99, 100 reserved for super admin)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="roleStatus">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="roleDescription" rows="3" placeholder="Describe the role's purpose and responsibilities..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Permissions</h6>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <button type="button" class="btn btn-sm btn-success" onclick="selectAllPermissions()">
                                        <i class="ri-check-double-line me-1"></i>Select All
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="deselectAllPermissions()">
                                        <i class="ri-close-line me-1"></i>Deselect All
                                    </button>
                                </div>
                                <div>
                                    <input type="text" class="form-control form-control-sm" id="searchPermissions" placeholder="Search permissions..." style="width: 200px;">
                                </div>
                            </div>
                            <div id="permissionsContainer" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <!-- Dynamic permissions will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveRoleBtn">Save Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Role Details Modal -->
<div class="modal fade" id="roleDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleDetailsTitle">Role Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="roleDetailsInfo"></div>
                    </div>
                    <div class="col-md-6">
                        <div id="roleDetailsStats"></div>
                    </div>
                </div>
                
                <ul class="nav nav-tabs mt-4" id="roleDetailsTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#rolePermissionsTab">Permissions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#roleUsersTab">Users</a>
                    </li>
                </ul>
                
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="rolePermissionsTab">
                        <div id="rolePermissionsList"></div>
                    </div>
                    <div class="tab-pane fade" id="roleUsersTab">
                        <div id="roleUsersList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1;
let currentFilters = {};
let availablePermissions = {};
let allRoles = [];

$(document).ready(function() {
    loadRoles();
    loadAvailablePermissions();
    loadStats();
    loadRoleHierarchy();
    
    // Search functionality
    $('#searchRoles').on('keyup', debounce(function() {
        applyFilters();
    }, 500));
    
    // Permission search in modal
    $('#searchPermissions').on('keyup', debounce(function() {
        filterPermissions();
    }, 300));
    
    // Form submission
    $('#roleForm').on('submit', function(e) {
        e.preventDefault();
        saveRole();
    });
});

function loadRoles(page = 1) {
    const params = new URLSearchParams({
        page: page,
        per_page: 15,
        ...currentFilters
    });
    
    $.ajax({
        url: `/api/admin/roles?${params}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                allRoles = response.data.roles;
                renderRolesTable(response.data.roles);
                renderPagination(response.data.pagination);
            }
        },
        error: function(xhr) {
            showAlert('Error loading roles', 'error');
        }
    });
}

function renderRolesTable(roles) {
    const tbody = $('#rolesTableBody');
    tbody.empty();
    
    roles.forEach(role => {
        const statusBadge = role.is_active 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
            
        const systemRole = ['super_admin', 'admin', 'customer', 'provider'].includes(role.name);
        const hierarchyBadge = getHierarchyBadge(role.hierarchy_level);
        
        const actions = `
            <div class="btn-group">
                <button class="btn btn-sm btn-info" onclick="viewRole(${role.id})" title="View Details">
                    <i class="ri-eye-line"></i>
                </button>
                @can('roles.manage.all')
                <button class="btn btn-sm btn-primary" onclick="editRole(${role.id})" title="Edit">
                    <i class="ri-edit-line"></i>
                </button>
                <button class="btn btn-sm btn-success" onclick="managePermissions(${role.id})" title="Manage Permissions">
                    <i class="ri-key-line"></i>
                </button>
                ${!systemRole ? `
                <button class="btn btn-sm btn-danger" onclick="deleteRole(${role.id}, '${role.name}')" title="Delete">
                    <i class="ri-delete-line"></i>
                </button>
                ` : ''}
                @endcan
            </div>
        `;
        
        tbody.append(`
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-sm rounded-circle bg-primary">
                                ${role.display_name.charAt(0)}
                            </span>
                        </div>
                        <div>
                            <strong>${role.display_name}</strong>
                            <br><code class="text-muted small">${role.name}</code>
                            ${systemRole ? '<span class="badge bg-warning ms-2">System</span>' : ''}
                        </div>
                    </div>
                </td>
                <td>${hierarchyBadge}</td>
                <td><span class="badge bg-info">${role.permissions?.length || 0} permissions</span></td>
                <td><span class="badge bg-secondary">${role.users_count || 0} users</span></td>
                <td>${statusBadge}</td>
                <td>${actions}</td>
            </tr>
        `);
    });
}

function getHierarchyBadge(level) {
    if (level >= 100) return '<span class="badge bg-danger">Super Admin (100)</span>';
    if (level >= 90) return '<span class="badge bg-primary">Admin (' + level + ')</span>';
    if (level >= 70) return '<span class="badge bg-success">Manager (' + level + ')</span>';
    if (level >= 50) return '<span class="badge bg-warning">Provider (' + level + ')</span>';
    return '<span class="badge bg-info">Customer (' + level + ')</span>';
}

function loadRoleHierarchy() {
    // Mock hierarchy data - replace with actual API call
    const hierarchy = allRoles.sort((a, b) => b.hierarchy_level - a.hierarchy_level);
    renderRoleHierarchy(hierarchy);
}

function renderRoleHierarchy(hierarchy) {
    const container = $('#roleHierarchy');
    container.empty();
    
    if (hierarchy.length === 0) {
        container.html('<div class="text-muted">No roles available</div>');
        return;
    }
    
    const hierarchyHtml = hierarchy.map(role => `
        <div class="text-center mx-3 mb-3">
            <div class="card border-0 shadow-sm" style="width: 150px;">
                <div class="card-body p-3">
                    <div class="avatar avatar-lg rounded-circle bg-primary mb-2">
                        ${role.display_name.charAt(0)}
                    </div>
                    <h6 class="card-title mb-1">${role.display_name}</h6>
                    <small class="text-muted">Level ${role.hierarchy_level}</small>
                    <br><span class="badge bg-info mt-1">${role.permissions?.length || 0} permissions</span>
                    <br><span class="badge bg-secondary mt-1">${role.users_count || 0} users</span>
                </div>
            </div>
        </div>
    `).join('');
    
    container.html(`
        <div class="d-flex flex-wrap justify-content-center align-items-center">
            ${hierarchyHtml}
        </div>
    `);
}

function openCreateRoleModal() {
    $('#roleModalTitle').text('Create Role');
    $('#roleForm')[0].reset();
    $('#roleId').val('');
    loadPermissionsForRole();
    $('#roleModal').modal('show');
}

function editRole(roleId) {
    $.ajax({
        url: `/api/admin/roles/${roleId}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                const role = response.data.role;
                
                $('#roleModalTitle').text('Edit Role');
                $('#roleId').val(role.id);
                $('#roleName').val(role.name);
                $('#roleDisplayName').val(role.display_name);
                $('#roleDescription').val(role.description);
                $('#roleHierarchy').val(role.hierarchy_level);
                $('#roleStatus').val(role.is_active ? '1' : '0');
                
                loadPermissionsForRole(role.permissions);
                $('#roleModal').modal('show');
            }
        },
        error: function(xhr) {
            showAlert('Error loading role details', 'error');
        }
    });
}

function loadAvailablePermissions() {
    $.ajax({
        url: '/api/admin/roles/available-permissions',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                availablePermissions = response.data.permissions;
            }
        }
    });
}

function loadPermissionsForRole(rolePermissions = []) {
    const container = $('#permissionsContainer');
    container.empty();
    
    const rolePermissionIds = rolePermissions.map(p => p.id);
    
    Object.keys(availablePermissions).forEach(module => {
        const modulePermissions = availablePermissions[module];
        
        container.append(`
            <div class="mb-4 permission-module" data-module="${module}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-primary text-uppercase mb-0">
                        <i class="bx bx-category me-2"></i>${module} Module
                    </h6>
                    <div>
                        <button type="button" class="btn btn-xs btn-outline-success" onclick="selectModulePermissions('${module}')">
                            Select All
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-warning" onclick="deselectModulePermissions('${module}')">
                            Deselect All
                        </button>
                    </div>
                </div>
                <div class="row">
                    ${modulePermissions.map(permission => `
                        <div class="col-md-6 mb-2 permission-item" data-permission="${permission.name.toLowerCase()}">
                            <div class="form-check">
                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                       id="perm_${permission.id}" 
                                       name="permissions[]" 
                                       value="${permission.id}"
                                       ${rolePermissionIds.includes(permission.id) ? 'checked' : ''}>
                                <label class="form-check-label" for="perm_${permission.id}">
                                    <strong>${permission.display_name}</strong>
                                    <br><code class="text-muted small">${permission.name}</code>
                                    ${permission.description ? `<br><small class="text-muted">${permission.description}</small>` : ''}
                                </label>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `);
    });
}

function filterPermissions() {
    const searchTerm = $('#searchPermissions').val().toLowerCase();
    
    if (searchTerm === '') {
        $('.permission-module, .permission-item').show();
        return;
    }
    
    $('.permission-item').each(function() {
        const permissionText = $(this).text().toLowerCase();
        const permissionName = $(this).data('permission');
        
        if (permissionText.includes(searchTerm) || permissionName.includes(searchTerm)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    
    // Hide modules with no visible permissions
    $('.permission-module').each(function() {
        const visibleItems = $(this).find('.permission-item:visible').length;
        if (visibleItems === 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });
}

function selectAllPermissions() {
    $('.permission-checkbox').prop('checked', true);
}

function deselectAllPermissions() {
    $('.permission-checkbox').prop('checked', false);
}

function selectModulePermissions(module) {
    $(`.permission-module[data-module="${module}"] .permission-checkbox`).prop('checked', true);
}

function deselectModulePermissions(module) {
    $(`.permission-module[data-module="${module}"] .permission-checkbox`).prop('checked', false);
}

function saveRole() {
    const roleId = $('#roleId').val();
    const isEdit = roleId !== '';
    
    const formData = {
        name: $('#roleName').val(),
        display_name: $('#roleDisplayName').val(),
        description: $('#roleDescription').val(),
        hierarchy_level: parseInt($('#roleHierarchy').val()),
        is_active: $('#roleStatus').val() === '1',
        permissions: []
    };
    
    // Collect selected permissions
    $('input[name="permissions[]"]:checked').each(function() {
        formData.permissions.push(parseInt($(this).val()));
    });
    
    const url = isEdit ? `/api/admin/roles/${roleId}` : '/api/admin/roles';
    const method = isEdit ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(formData),
        success: function(response) {
            if (response.success) {
                $('#roleModal').modal('hide');
                showAlert(response.message, 'success');
                loadRoles(currentPage);
                loadStats();
                loadRoleHierarchy();
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
                showAlert('Error saving role', 'error');
            }
        }
    });
}

function viewRole(roleId) {
    $.ajax({
        url: `/api/admin/roles/${roleId}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                const role = response.data.role;
                renderRoleDetails(role);
                $('#roleDetailsModal').modal('show');
            }
        },
        error: function(xhr) {
            showAlert('Error loading role details', 'error');
        }
    });
}

function renderRoleDetails(role) {
    $('#roleDetailsTitle').text(role.display_name + ' Details');
    
    // Basic info
    $('#roleDetailsInfo').html(`
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Basic Information</h6>
                <table class="table table-borderless">
                    <tr><td><strong>Name:</strong></td><td><code>${role.name}</code></td></tr>
                    <tr><td><strong>Display Name:</strong></td><td>${role.display_name}</td></tr>
                    <tr><td><strong>Description:</strong></td><td>${role.description || 'N/A'}</td></tr>
                    <tr><td><strong>Hierarchy Level:</strong></td><td>${getHierarchyBadge(role.hierarchy_level)}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>${role.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td></tr>
                    <tr><td><strong>Created:</strong></td><td>${new Date(role.created_at).toLocaleDateString()}</td></tr>
                </table>
            </div>
        </div>
    `);
    
    // Stats
    $('#roleDetailsStats').html(`
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Statistics</h6>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">${role.permissions?.length || 0}</h4>
                        <small class="text-muted">Permissions</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">${role.users_count || 0}</h4>
                        <small class="text-muted">Users</small>
                    </div>
                </div>
            </div>
        </div>
    `);
    
    // Permissions tab
    if (role.permissions && role.permissions.length > 0) {
        const groupedPermissions = role.permissions.reduce((acc, permission) => {
            if (!acc[permission.module]) {
                acc[permission.module] = [];
            }
            acc[permission.module].push(permission);
            return acc;
        }, {});
        
        let permissionsHtml = '';
        Object.keys(groupedPermissions).forEach(module => {
            permissionsHtml += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">${module.toUpperCase()} Module</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            ${groupedPermissions[module].map(permission => `
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ri-check-line text-success me-2"></i>
                                        <div>
                                            <strong>${permission.display_name}</strong>
                                            <br><code class="text-muted small">${permission.name}</code>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#rolePermissionsList').html(permissionsHtml);
    } else {
        $('#rolePermissionsList').html('<div class="alert alert-info">No permissions assigned to this role.</div>');
    }
}

function deleteRole(roleId, roleName) {
    Swal.fire({
        title: 'Delete Role?',
        text: `Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/roles/${roleId}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + getAuthToken(),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        loadRoles(currentPage);
                        loadStats();
                        loadRoleHierarchy();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    showAlert(response?.message || 'Error deleting role', 'error');
                }
            });
        }
    });
}

function loadStats() {
    // Mock stats - replace with actual API call
    $('#totalRoles').text(allRoles.length);
    $('#activeRoles').text(allRoles.filter(r => r.is_active).length);
    $('#usersWithRoles').text('0'); // Replace with actual count
    $('#totalPermissions').text('56'); // Replace with actual count
}

function applyFilters() {
    currentFilters = {
        search: $('#searchRoles').val(),
        active: $('#filterActive').val(),
        hierarchy_level: $('#filterHierarchy').val()
    };
    
    // Remove empty filters
    Object.keys(currentFilters).forEach(key => {
        if (!currentFilters[key]) {
            delete currentFilters[key];
        }
    });
    
    currentPage = 1;
    loadRoles(currentPage);
}

function refreshRoles() {
    currentFilters = {};
    $('#searchRoles').val('');
    $('#filterActive').val('');
    $('#filterHierarchy').val('');
    loadRoles();
    loadStats();
    loadRoleHierarchy();
}

function renderPagination(pagination) {
    const info = `Showing ${((pagination.current_page - 1) * pagination.per_page) + 1} to ${Math.min(pagination.current_page * pagination.per_page, pagination.total)} of ${pagination.total} roles`;
    $('#rolesInfo').text(info);
    
    let paginationHtml = '<ul class="pagination mb-0">';
    
    if (pagination.current_page > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadRoles(${pagination.current_page - 1})">Previous</a></li>`;
    }
    
    for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
        const active = i === pagination.current_page ? 'active' : '';
        paginationHtml += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadRoles(${i})">${i}</a></li>`;
    }
    
    if (pagination.current_page < pagination.last_page) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadRoles(${pagination.current_page + 1})">Next</a></li>`;
    }
    
    paginationHtml += '</ul>';
    $('#rolesPagination').html(paginationHtml);
}

// Utility functions
function getAuthToken() {
    return localStorage.getItem('auth_token') || '';
}

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
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
.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.permission-checkbox {
    cursor: pointer;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

code {
    font-size: 0.85em;
}

.badge {
    font-size: 0.75em;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
}

.modal-xl {
    max-width: 1200px;
}

.permission-module {
    border-left: 3px solid #6c5ffc;
    padding-left: 15px;
    margin-left: 10px;
}

.form-check-input:checked {
    background-color: #6c5ffc;
    border-color: #6c5ffc;
}

.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
}

.nav-tabs .nav-link.active {
    background-color: #6c5ffc;
    border-color: #6c5ffc;
    color: white;
}
</style>
@endsection