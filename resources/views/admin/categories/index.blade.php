@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Categories</h1>
            <div class="ms-md-1 ms-0">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="ri-add-line me-1"></i>Create Category
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">All Categories</div>
                    </div>
                    <div class="card-body">
                        <table id="categoriesTable" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Category Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createCategoryForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCategoryForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_category_id" name="category_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_status" name="status">
                                <label class="form-check-label" for="edit_status">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#categoriesTable').DataTable({
                responsive: true,
                scrollCollapse: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.categories.data') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columnDefs: [{
                    targets: [0],
                    visible: false,
                }],
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'no', name: 'no' },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // Prevent default form submission
            $('#createCategoryForm').on('submit', function(e) {
                e.preventDefault();
            });

            $('#editCategoryForm').on('submit', function(e) {
                e.preventDefault();
            });

            // jQuery Validation for Create Form
            $('#createCategoryForm').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2,
                        maxlength: 255
                    },
                    description: {
                        maxlength: 500
                    },
                    image: {
                        extension: "jpg|jpeg|png|gif|webp"
                    }
                },
                messages: {
                    name: {
                        required: "Category name is required",
                        minlength: "Category name must be at least 2 characters",
                        maxlength: "Category name cannot exceed 255 characters"
                    },
                    description: {
                        maxlength: "Description cannot exceed 500 characters"
                    },
                    image: {
                        extension: "Please select a valid image file (jpg, jpeg, png, gif, webp)"
                    }
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                validClass: 'is-valid',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.mb-3').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);

                    $.ajax({
                        url: '{{ route('admin.categories.store') }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#createCategoryModal').modal('hide');
                            $('#createCategoryForm')[0].reset();
                            $('#createCategoryForm').find('.is-valid, .is-invalid')
                                .removeClass('is-valid is-invalid');
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    var element = $('#' + key);
                                    element.addClass('is-invalid');
                                    element.closest('.mb-3').find(
                                        '.invalid-feedback').text(value[0]);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Something went wrong. Please try again.'
                                });
                            }
                        }
                    });
                }
            });

            // jQuery Validation for Edit Form
            $('#editCategoryForm').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2,
                        maxlength: 255
                    },
                    description: {
                        maxlength: 500
                    },
                    image: {
                        extension: "jpg|jpeg|png|gif|webp"
                    }
                },
                messages: {
                    name: {
                        required: "Category name is required",
                        minlength: "Category name must be at least 2 characters",
                        maxlength: "Category name cannot exceed 255 characters"
                    },
                    description: {
                        maxlength: "Description cannot exceed 500 characters"
                    },
                    image: {
                        extension: "Please select a valid image file (jpg, jpeg, png, gif, webp)"
                    }
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                validClass: 'is-valid',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.mb-3').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    var categoryId = $('#edit_category_id').val();

                    $.ajax({
                        url: '{{ route('admin.categories.update', '') }}/' + categoryId,
                        type: 'PUT',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#editCategoryModal').modal('hide');
                            $('#editCategoryForm')[0].reset();
                            $('#editCategoryForm').find('.is-valid, .is-invalid')
                                .removeClass('is-valid is-invalid');
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    var element = $('#edit_' + key);
                                    element.addClass('is-invalid');
                                    element.closest('.mb-3').find(
                                        '.invalid-feedback').text(value[0]);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Something went wrong. Please try again.'
                                });
                            }
                        }
                    });
                }
            });

            // Edit Category function
            window.editCategory = function(id) {
                $.ajax({
                    url: '{{ route('admin.categories.show', '') }}/' + id,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var category = response.data;
                            $('#edit_category_id').val(category.id);
                            $('#edit_name').val(category.name);
                            $('#edit_description').val(category.description);
                            $('#edit_status').prop('checked', category.status == 1);
                            $('#editCategoryModal').modal('show');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to load category data.'
                        });
                    }
                });
            };

            // Delete Category function
            window.deleteCategory = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.categories.destroy', '') }}/' + id,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to delete category.'
                                });
                            }
                        });
                    }
                });
            };
        });
    </script>
@endsection
