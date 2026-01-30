@extends('admin.layouts.master')

@section('title', 'Permission Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Permission Management</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Permissions</li>
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
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-success">
                                <i class="bx bx-category fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Modules</p>
                                    <h4 class="fw-semibold mt-1" id="totalModules">0</h4>
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
                                <i class="bx bx-cog fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Actions</p>
                                    <h4 class="fw-semibold mt-1" id="totalActions">0</h4>
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
                                    <p class="text-muted mb-0">Assigned to Roles</p>
                                    <h4 class="fw-semibold mt-1" id="assignedPermissions">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Management Card -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        <i class="bx bx-key me-2"></i>Permissions Management
                    </div>
                    <div class="d-flex">
                        @can('permissions.manage.all')
                        <button class="btn btn-sm btn-primary me-2" onclick="openCreatePermissionModal()">
                            <i class="ri-add-line me-1"></i>Create Permission
                        </button>
                        <button class="btn btn-sm btn-success me-2" onclick="openBulkCreateModal()">
                            <i class="ri-add-multiple-line me-1"></i>Bulk Create
                        </button>
                        @endcan
                        <button class="btn btn-sm btn-secondary" onclick="refreshPermissions()">
                            <i class="ri-refresh-line me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Search Permissions</label>
                            <input type="text" class="form-control" id="searchPermissions" placeholder="Search by name or description...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Module</label>
                            <select class="form-select" id="filterModule">
                                <option value="">All Modules</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Action</label>
                            <select class="form-select" id="filterAction">
                                <option value="">All Actions</option>
                                <option value="create">Create</option>
                                <option value="read">Read</option>
                                <option value="update">Update</option>
                                <option value="delete">Delete</option>
                                <option value="manage">Manage</option>
                                <option value="view">View</option>
                                <option value="assign">Assign</option>
                                <option value="export">Export</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Resource</label>
                            <select class="form-select" id="filterResource">
                                <option value="">All Resources</option>
                                <option value="all">All</option>
                                <option value="own">Own</option>
                                <option value="assigned">Assigned</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">View</label>
                            <select class="form-select" id="viewMode">
                                <option value="table">Table View</option>
                                <option value="cards">Card View</option>
                                <option value="grouped">Grouped View</option>
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
                            <table class="table text-nowrap table-hover" id="permissionsTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Permission Name</th>
                                        <th>Display Name</th>
                                        <th>Module</th>
                                        <th>Action</th>
                                        <th>Resource</th>
                                        <th>Roles</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="permissionsTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card View -->
                    <div id="cardView" style="display: none;">
                        <div class="row" id="permissionsCards">
                            <!-- Dynamic cards -->
                        </div>
                    </div>

                    <!-- Grouped View -->
                    <div id="groupedView" style="display: none;">
                        <div id="permissionsGrouped">
                            <!-- Dynamic grouped content -->
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div id="bulkActions" style="display: none;">
                            <button class="btn btn-sm btn-danger" onclick="bulkDelete()">
                                <i class="ri-delete-line me-1"></i>Delete Selected
                            </button>
                            <button class="btn btn-sm btn-info" onclick="bulkAssignToRole()">
                                <i class="ri-shield-line me-1"></i>Assign to Role
                            </button>
                        </div>
                        <div id="permissionsInfo"></div>
                        <nav id="permissionsPagination"></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalTitle">Create Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="permissionForm">
                <div class="modal-body">
                    <input type="hidden" id="permissionId">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Module <span class="text-danger">*</span></label>
                                <select class="form-select" id="permissionModule" required>
                                    <option value="">Select Module</option>
                                    <option value="dashboard">Dashboard</option>
                                    <option value="users">Users</option>
                                    <option value="roles">Roles</option>
                                    <option value="permissions">Permissions</option>
                                    <option value="categories">Categories</option>
                                    <option value="services">Services</option>
                                    <option value="bookings">Bookings</option>
                                    <option value="payments">Payments</option>
                                    <option value="providers">Providers</option>
                                    <option value="customers">Customers</option>
                                    <option value="reports">Reports</option>
                                    <option value="settings">Settings</option>
                                    <option value="audit">Audit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Action <span class="text-danger">*</span></label>
                                <select class="form-select" id="permissionAction" required>
                                    <option value="">Select Action</option>
                                    <option value="create">Create</option>
                                    <option value="read">Read</option>
                                    <option value="update">Update</option>
                                    <option value="delete">Delete</option>
                                    <option value="manage">Manage</option>
                                    <option value="view">View</option>
                                    <option value="assign">Assign</option>
                                    <option value="export">Export</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Resource <span class="text-danger">*</span></label>
                                <select class="form-select" id="permissionResource" required>
                                    <option value="">Select Resource</option>
                                    <option value="all">All</option>
                                    <option value="own">Own</option>
                                    <option value="assigned">Assigned</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="permissionName" readonly>
                        <small class="text-muted">Auto-generated from module.action.resource</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="permissionDisplayName" required>
                        <small class="text-muted">Human-readable name for the permission</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="permissionDescription" rows="3" placeholder="Describe what this permission allows..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign to Roles (Optional)</label>
                        <div id="rolesContainer" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <!-- Dynamic roles will be loaded here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="savePermissionBtn">Save Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Create Modal -->
<div class="modal fade" id="bulkCreateModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Create Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Select Modules</h6>
                        <div id="bulkModules" class="mb-3">
                            <!-- Dynamic checkboxes -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Select Actions</h6>
                        <div id="bulkActions" class="mb-3">
                            <!-- Dynamic checkboxes -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h6>Select Resources</h6>
                        <div id="bulkResources" class="mb-3">
                            <!-- Dynamic checkboxes -->
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    This will create permissions for all combinations of selected modules, actions, and resources.
                    <strong id="combinationCount">0</strong> permissions will be created.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="bulkCreatePermissions()">Create Permissions</button>
            </div>
        </div>
    </div>
</div>

<!-- Permission Details Modal -->
<div class="modal fade" id="permissionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionDetailsTitle">Permission Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="permissionDetailsContent">
                    <!-- Dynamic content -->
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
let selectedPermissions = [];
let allRoles = [];

$(document).ready(function() {
    loadPermissions();
    loadRoles();
    loadStats();
    
    // Search functionality
    $('#searchPermissions').on('keyup', debounce(function() {
        applyFilters();
    }, 500));
    
    // View mode change
    $('#viewMode').on('change', function() {
        switchView($(this).val());
    });
    
    // Form submission
    $('#permissionForm').on('submit', function(e) {
        e.preventDefault();
        savePermission();
    });
    
    // Auto-generate permission name
    $('#permissionModule, #permissionAction, #permissionResource').on('change', function() {
        generatePermissionName();
    });
    
    // Select all functionality
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.permission-checkbox').prop('checked', isChecked);
        updateBulkActions();
    });
    
    // Individual checkbox change
    $(document).on('change', '.permission-checkbox', function() {
        updateBulkActions();
    });
});

function loadPermissions(page = 1) {
    const params = new URLSearchParams({
        page: page,
        per_page: 15,
        ...currentFilters
    });
    
    showLoading();
    
    $.ajax({
        url: `/api/admin/permissions?${params}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                const viewMode = $('#viewMode').val();
                switch(viewMode) {
                    case 'table':
                        renderPermissionsTable(response.data.permissions);
                        break;
                    case 'cards':
                        renderPermissionsCards(response.data.permissions);
                        break;
                    case 'grouped':
                        renderPermissionsGrouped(response.data.permissions);
                        break;
                }
                renderPagination(response.data.pagination);
            }
        },
        error: function(xhr) {
            showAlert('Error loading permissions', 'error');
        },
        complete: function() {
            hideLoading();
        }
    });
}

function renderPermissionsTable(permissions) {
    const tbody = $('#permissionsTableBody');
    tbody.empty();
    
    permissions.forEach(permission => {
        const rolesCount = permission.roles ? permission.roles.length : 0;
        const rolesBadge = rolesCount > 0 
            ? `<span class="badge bg-info">${rolesCount} roles</span>`
            : '<span class="badge bg-secondary">No roles</span>';
            
        tbody.append(`
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input permission-checkbox" value="${permission.id}">
                </td>
                <td>
                    <code class="text-primary">${permission.name}</code>
                </td>
                <td>
                    <strong>${permission.display_name}</strong>
                    ${permission.description ? `<br><small class="text-muted">${permission.description}</small>` : ''}
                </td>
                <td><span class="badge bg-primary">${permission.module}</span></td>
                <td><span class="badge bg-success">${permission.action}</span></td>
                <td><span class="badge bg-warning">${permission.resource}</span></td>
                <td>${rolesBadge}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info" onclick="viewPermission(${permission.id})" title="View Details">
                            <i class="ri-eye-line"></i>
                        </button>
                        @can('permissions.manage.all')
                        <button class="btn btn-sm btn-primary" onclick="editPermission(${permission.id})" title="Edit">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deletePermission(${permission.id}, '${permission.name}')" title="Delete">
                            <i class="ri-delete-line"></i>
                        </button>
                        @endcan
                    </div>
                </td>
            </tr>
        `);
    });
}

function renderPermissionsCards(permissions) {
    const container = $('#permissionsCards');
    container.empty();
    
    permissions.forEach(permission => {
        const rolesCount = permission.roles ? permission.roles.length : 0;
        
        container.append(`
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">${permission.display_name}</h6>
                                <code class="text-primary small">${permission.name}</code>
                            </div>
                            <input type="checkbox" class="form-check-input permission-checkbox" value="${permission.id}">
                        </div>
                        
                        ${permission.description ? `<p class="text-muted small mb-2">${permission.description}</p>` : ''}
                        
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <span class="badge bg-primary">${permission.module}</span>
                            <span class="badge bg-success">${permission.action}</span>
                            <span class="badge bg-warning">${permission.resource}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info">${rolesCount} roles</span>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info" onclick="viewPermission(${permission.id})">
                                    <i class="ri-eye-line"></i>
                                </button>
                                @can('permissions.manage.all')
                                <button class="btn btn-sm btn-primary" onclick="editPermission(${permission.id})">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deletePermission(${permission.id}, '${permission.name}')">
                                    <i class="ri-delete-line"></i>
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

function renderPermissionsGrouped(permissions) {
    const container = $('#permissionsGrouped');
    container.empty();
    
    // Group permissions by module
    const grouped = permissions.reduce((acc, permission) => {
        if (!acc[permission.module]) {
            acc[permission.module] = [];
        }
        acc[permission.module].push(permission);
        return acc;
    }, {});
    
    Object.keys(grouped).forEach(module => {
        const modulePermissions = grouped[module];
        
        container.append(`
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bx bx-category me-2"></i>${module.toUpperCase()} Module
                        <span class="badge bg-primary ms-2">${modulePermissions.length} permissions</span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        ${modulePermissions.map(permission => `
                            <div class="col-md-6 mb-2">
                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                    <div class="flex-grow-1">
                                        <strong class="small">${permission.display_name}</strong>
                                        <br><code class="text-primary small">${permission.name}</code>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input permission-checkbox" value="${permission.id}">
                                        <div class="btn-group">
                                            <button class="btn btn-xs btn-info" onclick="viewPermission(${permission.id})">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @can('permissions.manage.all')
                                            <button class="btn btn-xs btn-primary" onclick="editPermission(${permission.id})">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `);
    });
}

function switchView(viewMode) {
    $('#tableView, #cardView, #groupedView').hide();
    $(`#${viewMode}View`).show();
    loadPermissions(currentPage);
}

function generatePermissionName() {
    const module = $('#permissionModule').val();
    const action = $('#permissionAction').val();
    const resource = $('#permissionResource').val();
    
    if (module && action && resource) {
        const permissionName = `${module}.${action}.${resource}`;
        $('#permissionName').val(permissionName);
        
        // Auto-generate display name
        if (!$('#permissionDisplayName').val()) {
            const displayName = `${action.charAt(0).toUpperCase() + action.slice(1)} ${resource === 'all' ? 'All' : resource.charAt(0).toUpperCase() + resource.slice(1)} ${module.charAt(0).toUpperCase() + module.slice(1)}`;
            $('#permissionDisplayName').val(displayName);
        }
    }
}

function openCreatePermissionModal() {
    $('#permissionModalTitle').text('Create Permission');
    $('#permissionForm')[0].reset();
    $('#permissionId').val('');
    loadRolesForPermission();
    $('#permissionModal').modal('show');
}

function editPermission(permissionId) {
    $.ajax({
        url: `/api/admin/permissions/${permissionId}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                const permission = response.data.permission;
                
                $('#permissionModalTitle').text('Edit Permission');
                $('#permissionId').val(permission.id);
                $('#permissionModule').val(permission.module);
                $('#permissionAction').val(permission.action);
                $('#permissionResource').val(permission.resource);
                $('#permissionName').val(permission.name);
                $('#permissionDisplayName').val(permission.display_name);
                $('#permissionDescription').val(permission.description);
                
                loadRolesForPermission(permission.roles);
                $('#permissionModal').modal('show');
            }
        },
        error: function(xhr) {
            showAlert('Error loading permission details', 'error');
        }
    });
}

function viewPermission(permissionId) {
    $.ajax({
        url: `/api/admin/permissions/${permissionId}`,
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                const permission = response.data.permission;
                renderPermissionDetails(permission);
                $('#permissionDetailsModal').modal('show');
            }
        },
        error: function(xhr) {
            showAlert('Error loading permission details', 'error');
        }
    });
}

function renderPermissionDetails(permission) {
    $('#permissionDetailsTitle').text(permission.display_name + ' Details');
    
    const rolesHtml = permission.roles && permission.roles.length > 0
        ? permission.roles.map(role => `<span class="badge bg-primary me-1">${role.display_name}</span>`).join('')
        : '<span class="text-muted">No roles assigned</span>';
    
    $('#permissionDetailsContent').html(`
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><td><strong>Name:</strong></td><td><code>${permission.name}</code></td></tr>
                    <tr><td><strong>Display Name:</strong></td><td>${permission.display_name}</td></tr>
                    <tr><td><strong>Module:</strong></td><td><span class="badge bg-primary">${permission.module}</span></td></tr>
                    <tr><td><strong>Action:</strong></td><td><span class="badge bg-success">${permission.action}</span></td></tr>
                    <tr><td><strong>Resource:</strong></td><td><span class="badge bg-warning">${permission.resource}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><td><strong>Description:</strong></td><td>${permission.description || 'N/A'}</td></tr>
                    <tr><td><strong>Assigned Roles:</strong></td><td>${rolesHtml}</td></tr>
                    <tr><td><strong>Created:</strong></td><td>${new Date(permission.created_at).toLocaleDateString()}</td></tr>
                </table>
            </div>
        </div>
    `);
}

function openBulkCreateModal() {
    loadBulkCreateOptions();
    $('#bulkCreateModal').modal('show');
}

function loadBulkCreateOptions() {
    const modules = ['dashboard', 'users', 'roles', 'permissions', 'categories', 'services', 'bookings', 'payments', 'providers', 'customers', 'reports', 'settings', 'audit'];
    const actions = ['create', 'read', 'update', 'delete', 'manage', 'view', 'assign', 'export'];
    const resources = ['all', 'own', 'assigned'];
    
    // Populate modules
    const modulesContainer = $('#bulkModules');
    modulesContainer.empty();
    modules.forEach(module => {
        modulesContainer.append(`
            <div class="form-check">
                <input class="form-check-input bulk-module" type="checkbox" value="${module}" id="bulk_module_${module}">
                <label class="form-check-label" for="bulk_module_${module}">${module.charAt(0).toUpperCase() + module.slice(1)}</label>
            </div>
        `);
    });
    
    // Populate actions
    const actionsContainer = $('#bulkActions');
    actionsContainer.empty();
    actions.forEach(action => {
        actionsContainer.append(`
            <div class="form-check">
                <input class="form-check-input bulk-action" type="checkbox" value="${action}" id="bulk_action_${action}">
                <label class="form-check-label" for="bulk_action_${action}">${action.charAt(0).toUpperCase() + action.slice(1)}</label>
            </div>
        `);
    });
    
    // Populate resources
    const resourcesContainer = $('#bulkResources');
    resourcesContainer.empty();
    resources.forEach(resource => {
        resourcesContainer.append(`
            <div class="form-check">
                <input class="form-check-input bulk-resource" type="checkbox" value="${resource}" id="bulk_resource_${resource}">
                <label class="form-check-label" for="bulk_resource_${resource}">${resource.charAt(0).toUpperCase() + resource.slice(1)}</label>
            </div>
        `);
    });
    
    // Update combination count on change
    $(document).on('change', '.bulk-module, .bulk-action, .bulk-resource', updateCombinationCount);
}

function updateCombinationCount() {
    const moduleCount = $('.bulk-module:checked').length;
    const actionCount = $('.bulk-action:checked').length;
    const resourceCount = $('.bulk-resource:checked').length;
    
    const totalCombinations = moduleCount * actionCount * resourceCount;
    $('#combinationCount').text(totalCombinations);
}

function bulkCreatePermissions() {
    const modules = $('.bulk-module:checked').map(function() { return $(this).val(); }).get();
    const actions = $('.bulk-action:checked').map(function() { return $(this).val(); }).get();
    const resources = $('.bulk-resource:checked').map(function() { return $(this).val(); }).get();
    
    if (modules.length === 0 || actions.length === 0 || resources.length === 0) {
        showAlert('Please select at least one option from each category', 'warning');
        return;
    }
    
    $.ajax({
        url: '/api/admin/permissions/bulk-create',
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            modules: modules,
            actions: actions,
            resources: resources
        }),
        success: function(response) {
            if (response.success) {
                $('#bulkCreateModal').modal('hide');
                showAlert(response.message, 'success');
                loadPermissions(currentPage);
                loadStats();
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert(response?.message || 'Error creating permissions', 'error');
        }
    });
}

function bulkDelete() {
    if (selectedPermissions.length === 0) {
        showAlert('Please select permissions to delete', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Delete Selected Permissions?',
        text: `Are you sure you want to delete ${selectedPermissions.length} permissions? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement bulk delete API call
            showAlert('Bulk delete functionality will be implemented', 'info');
        }
    });
}

function bulkAssignToRole() {
    if (selectedPermissions.length === 0) {
        showAlert('Please select permissions to assign', 'warning');
        return;
    }
    
    // Show role selection modal
    showAlert('Bulk role assignment functionality will be implemented', 'info');
}

function savePermission() {
    const permissionId = $('#permissionId').val();
    const isEdit = permissionId !== '';
    
    const formData = {
        module: $('#permissionModule').val(),
        action: $('#permissionAction').val(),
        resource: $('#permissionResource').val(),
        display_name: $('#permissionDisplayName').val(),
        description: $('#permissionDescription').val(),
        roles: []
    };
    
    // Collect selected roles
    $('input[name="permission_roles[]"]:checked').each(function() {
        formData.roles.push(parseInt($(this).val()));
    });
    
    const url = isEdit ? `/api/admin/permissions/${permissionId}` : '/api/admin/permissions';
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
                $('#permissionModal').modal('hide');
                showAlert(response.message, 'success');
                loadPermissions(currentPage);
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
                showAlert('Error saving permission', 'error');
            }
        }
    });
}

function deletePermission(permissionId, permissionName) {
    Swal.fire({
        title: 'Delete Permission?',
        text: `Are you sure you want to delete the permission "${permissionName}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/permissions/${permissionId}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + getAuthToken(),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        loadPermissions(currentPage);
                        loadStats();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    showAlert(response?.message || 'Error deleting permission', 'error');
                }
            });
        }
    });
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
            }
        }
    });
}

function loadRolesForPermission(selectedRoles = []) {
    const container = $('#rolesContainer');
    container.empty();
    
    const selectedRoleIds = selectedRoles.map(role => role.id);
    
    allRoles.forEach(role => {
        const isChecked = selectedRoleIds.includes(role.id) ? 'checked' : '';
        container.append(`
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" 
                       id="role_${role.id}" 
                       name="permission_roles[]" 
                       value="${role.id}" ${isChecked}>
                <label class="form-check-label" for="role_${role.id}">
                    ${role.display_name}
                    <small class="text-muted d-block">${role.description}</small>
                </label>
            </div>
        `);
    });
}

function loadStats() {
    $.ajax({
        url: '/api/admin/permissions/stats',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken(),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                $('#totalPermissions').text(stats.total_permissions || 0);
                $('#totalModules').text(stats.total_modules || 0);
                $('#totalActions').text(stats.total_actions || 0);
                $('#assignedPermissions').text(stats.assigned_permissions || 0);
                
                // Update filter dropdowns
                const moduleFilter = $('#filterModule');
                moduleFilter.find('option:not(:first)').remove();
                if (stats.modules) {
                    stats.modules.forEach(module => {
                        moduleFilter.append(`<option value="${module}">${module}</option>`);
                    });
                }
            }
        }
    });
}

function applyFilters() {
    currentFilters = {
        search: $('#searchPermissions').val(),
        module: $('#filterModule').val(),
        action: $('#filterAction').val(),
        resource: $('#filterResource').val()
    };
    
    // Remove empty filters
    Object.keys(currentFilters).forEach(key => {
        if (!currentFilters[key]) {
            delete currentFilters[key];
        }
    });
    
    currentPage = 1;
    loadPermissions(currentPage);
}

function refreshPermissions() {
    currentFilters = {};
    $('#searchPermissions').val('');
    $('#filterModule').val('');
    $('#filterAction').val('');
    $('#filterResource').val('');
    loadPermissions();
    loadStats();
}

function updateBulkActions() {
    const checkedBoxes = $('.permission-checkbox:checked');
    selectedPermissions = checkedBoxes.map(function() {
        return parseInt($(this).val());
    }).get();
    
    if (selectedPermissions.length > 0) {
        $('#bulkActions').show();
    } else {
        $('#bulkActions').hide();
    }
}

function renderPagination(pagination) {
    const info = `Showing ${((pagination.current_page - 1) * pagination.per_page) + 1} to ${Math.min(pagination.current_page * pagination.per_page, pagination.total)} of ${pagination.total} permissions`;
    $('#permissionsInfo').text(info);
    
    let paginationHtml = '<ul class="pagination mb-0">';
    
    if (pagination.current_page > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadPermissions(${pagination.current_page - 1})">Previous</a></li>`;
    }
    
    for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
        const active = i === pagination.current_page ? 'active' : '';
        paginationHtml += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadPermissions(${i})">${i}</a></li>`;
    }
    
    if (pagination.current_page < pagination.last_page) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadPermissions(${pagination.current_page + 1})">Next</a></li>`;
    }
    
    paginationHtml += '</ul>';
    $('#permissionsPagination').html(paginationHtml);
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

.modal-lg {
    max-width: 800px;
}

.modal-xl {
    max-width: 1200px;
}
</style>
@endsection