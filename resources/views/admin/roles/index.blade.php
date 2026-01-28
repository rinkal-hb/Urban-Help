@extends('admin.layouts.master')

@section('title', 'Role Management')

@section('styles')
@endsection
@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Role Management</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Roles</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Roles Management -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">All Roles</h5>
                    @can('roles.manage.all')
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-2"></i>Create New Role
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="status-filter">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="search-input" placeholder="Search roles...">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary w-100" id="filter-btn">
                                <i class="bx bx-search me-2"></i>Filter
                            </button>
                        </div>
                    </div>

                    <!-- Roles Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="roles-table">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Display Name</th>
                                    <th>Permissions</th>
                                    <th>Users</th>
                                    <th>Hierarchy</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <span id="showing-from">0</span> to <span id="showing-to">0</span> of <span
                                id="total-roles">0</span> roles
                        </div>
                        <nav>
                            <ul class="pagination mb-0" id="pagination">
                                <!-- Pagination will be generated via JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Details Modal -->
    <div class="modal fade" id="roleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Role Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="role-details-content">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let perPage = 15;

            // Load roles data
            function loadRoles(page = 1) {
                const filters = {
                    page: page,
                    per_page: perPage,
                    active: $('#status-filter').val(),
                    search: $('#search-input').val()
                };

                $.ajax({
                    url: '/api/admin/roles',
                    method: 'GET',
                    data: filters,
                    headers: {
                        'Authorization': 'Bearer ' + $('meta[name="api-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            renderRolesTable(response.data.roles);
                            renderPagination(response.data.pagination);
                            updateShowingInfo(response.data.pagination);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading roles:', xhr.responseJSON);
                        Swal.fire('Error', 'Failed to load roles', 'error');
                    }
                });
            }

            // Render roles table
            function renderRolesTable(roles) {
                const tbody = $('#roles-table tbody');
                tbody.empty();

                if (roles.length === 0) {
                    tbody.append(`
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No roles found</td>
                </tr>
            `);
                    return;
                }

                roles.forEach(role => {
                    const statusBadge = role.is_active ?
                        '<span class="badge bg-success">Active</span>' :
                        '<span class="badge bg-danger">Inactive</span>';

                    const hierarchyBadge = getHierarchyBadge(role.hierarchy_level);

                    tbody.append(`
                <tr>
                    <td>
                        <div class="fw-semibold">${role.name}</div>
                        <div class="text-muted fs-12">${role.description || 'No description'}</div>
                    </td>
                    <td>${role.display_name}</td>
                    <td>
                        <span class="badge bg-info">${role.permissions ? role.permissions.length : 0} permissions</span>
                    </td>
                    <td>
                        <span class="badge bg-primary">${role.users_count || 0} users</span>
                    </td>
                    <td>${hierarchyBadge}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewRole(${role.id})">
                                <i class="bx bx-show"></i>
                            </button>
                            @can('roles.manage.all')
                            ${role.name !== 'super_admin' ? `
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="editRole(${role.id})">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRole(${role.id})">
                                    <i class="bx bx-trash"></i>
                                </button>
                                ` : ''}
                            @endcan
                        </div>
                    </td>
                </tr>
            `);
                });
            }

            // Get hierarchy badge
            function getHierarchyBadge(level) {
                if (level >= 90) return '<span class="badge bg-danger">Super Admin</span>';
                if (level >= 80) return '<span class="badge bg-warning">Admin</span>';
                if (level >= 50) return '<span class="badge bg-info">Manager</span>';
                return '<span class="badge bg-secondary">User</span>';
            }

            // Render pagination
            function renderPagination(pagination) {
                const paginationEl = $('#pagination');
                paginationEl.empty();

                if (pagination.last_page <= 1) return;

                // Previous button
                paginationEl.append(`
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
            </li>
        `);

                // Page numbers
                for (let i = 1; i <= pagination.last_page; i++) {
                    if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 2 && i <=
                            pagination.current_page + 2)) {
                        paginationEl.append(`
                    <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
                    } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                        paginationEl.append(
                            '<li class="page-item disabled"><span class="page-link">...</span></li>');
                    }
                }

                // Next button
                paginationEl.append(`
            <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
            </li>
        `);
            }

            // Update showing info
            function updateShowingInfo(pagination) {
                const from = (pagination.current_page - 1) * pagination.per_page + 1;
                const to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

                $('#showing-from').text(from);
                $('#showing-to').text(to);
                $('#total-roles').text(pagination.total);
            }

            // Event listeners
            $('#filter-btn').click(function() {
                currentPage = 1;
                loadRoles(currentPage);
            });

            $('#search-input').keypress(function(e) {
                if (e.which === 13) {
                    currentPage = 1;
                    loadRoles(currentPage);
                }
            });

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page && page !== currentPage) {
                    currentPage = page;
                    loadRoles(currentPage);
                }
            });

            // Initial load
            loadRoles();
        });

        // Role actions
        function viewRole(roleId) {
            $.ajax({
                url: `/api/admin/roles/${roleId}`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + $('meta[name="api-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        const role = response.data.role;
                        let permissionsHtml = '';

                        if (role.permissions && role.permissions.length > 0) {
                            const groupedPermissions = {};
                            role.permissions.forEach(permission => {
                                if (!groupedPermissions[permission.module]) {
                                    groupedPermissions[permission.module] = [];
                                }
                                groupedPermissions[permission.module].push(permission);
                            });

                            Object.keys(groupedPermissions).forEach(module => {
                                permissionsHtml +=
                                    `<h6 class="mt-3">${module.charAt(0).toUpperCase() + module.slice(1)}</h6>`;
                                permissionsHtml += '<div class="d-flex flex-wrap gap-1">';
                                groupedPermissions[module].forEach(permission => {
                                    permissionsHtml +=
                                        `<span class="badge bg-info">${permission.display_name}</span>`;
                                });
                                permissionsHtml += '</div>';
                            });
                        } else {
                            permissionsHtml = '<p class="text-muted">No permissions assigned</p>';
                        }

                        $('#role-details-content').html(`
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <p><strong>Name:</strong> ${role.name}</p>
                            <p><strong>Display Name:</strong> ${role.display_name}</p>
                            <p><strong>Description:</strong> ${role.description || 'No description'}</p>
                            <p><strong>Hierarchy Level:</strong> ${role.hierarchy_level}</p>
                            <p><strong>Status:</strong> ${role.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Statistics</h6>
                            <p><strong>Users:</strong> ${role.users ? role.users.length : 0}</p>
                            <p><strong>Permissions:</strong> ${role.permissions ? role.permissions.length : 0}</p>
                            <p><strong>Created:</strong> ${new Date(role.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Permissions</h6>
                    ${permissionsHtml}
                `);

                        $('#roleDetailsModal').modal('show');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to load role details', 'error');
                }
            });
        }

        function editRole(roleId) {
            window.location.href = `/admin/roles/${roleId}/edit`;
        }

        function deleteRole(roleId) {
            Swal.fire({
                title: 'Delete Role?',
                text: 'This action cannot be undone! All users with this role will lose their permissions.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/admin/roles/${roleId}`,
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + $('meta[name="api-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success');
                                loadRoles(currentPage);
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to delete role',
                                'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
