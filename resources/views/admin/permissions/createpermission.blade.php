@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Permissions</h1>
            <div class="ms-md-1 ms-0">
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#createModuleModal">
                    <i class="ri-add-line me-1"></i>Create Module
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                    <i class="ri-add-line me-1"></i>Create Permission
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">All Permissions</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Module</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>users.create</td>
                                        <td>User Management</td>
                                        <td>Create new users</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">Edit</button>
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Permission Modal -->
    <div class="modal fade" id="createPermissionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createPermissionForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Permission Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="module" class="form-label">Module</label>
                            <div class="input-group">
                                <select class="form-select" id="module" name="module" required>
                                    <option value="">Select Module</option>
                                    <option value="User Management">User Management</option>
                                    <option value="Role Management">Role Management</option>
                                    <option value="Permission Management">Permission Management</option>
                                    <option value="Category Management">Category Management</option>
                                    <option value="Service Management">Service Management</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createModuleModal">
                                    <i class="ri-add-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="createPermissionForm" class="btn btn-primary">Create Permission</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.components.create-module-modal')
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#createModuleForm').on('submit', function(e) {
        e.preventDefault();
        var moduleName = $('#module_name').val();
        
        // Add new module to dropdown
        $('#module').append('<option value="' + moduleName + '">' + moduleName + '</option>');
        $('#module').val(moduleName);
        
        // Close modal and reset form
        $('#createModuleModal').modal('hide');
        $('#createModuleForm')[0].reset();
        
        // Show success message
        alert('Module created successfully!');
    });
});
</script>
@endsection
