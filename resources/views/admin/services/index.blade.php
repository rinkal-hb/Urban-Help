@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Services</h1>
            <div class="ms-md-1 ms-0">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                    <i class="ri-add-line me-1"></i>Create Service
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">All Services</div>
                    </div>
                    <div class="card-body">
                        <table id="servicesTable" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Duration</th>
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

    <!-- Create Service Modal -->
    <div class="modal fade" id="createServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createServiceForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01"
                                        min="0">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (minutes) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="duration" name="duration"
                                        min="1">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
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
                        <button type="submit" class="btn btn-primary">Create Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editServiceForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_service_id" name="service_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_category_id" class="form-label">Category <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="edit_category_id" name="category_id">
                                <option value="">Select Category</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Service Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_price" class="form-label">Price ($) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_price" name="price"
                                        step="0.01" min="0">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_duration" class="form-label">Duration (minutes) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_duration" name="duration"
                                        min="1">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
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
                        <button type="submit" class="btn btn-primary">Update Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Load categories
            loadCategories();

            var table = $('#servicesTable').DataTable({
                    responsive: true,
                    scrollCollapse: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.services.data') }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    },
                    columnDefs: [{
                        targets: [0],
                        visible: false,
                    }],
                    columns: [{}
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'no',
                        name: 'no'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'duration',
                        name: 'duration'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

        function loadCategories() {
            $.ajax({
                url: '{{ route('admin.services.categories') }}',
                type: 'GET',
                success: function(response) {
                    var options = '<option value="">Select Category</option>';
                    $.each(response, function(index, category) {
                        options += '<option value="' + category.id + '">' + category.name +
                            '</option>';
                    });
                    $('#category_id, #edit_category_id').html(options);
                }
            });
        }

        // Status toggle
        $(document).on('change', '.status-toggle', function() {
            var id = $(this).data('id');
            var status = $(this).is(':checked');

            $.ajax({
                url: '{{ route('admin.services.toggle-status', '') }}/' + id,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Updated!',
                            text: 'Service status has been updated.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function() {
                    // Revert checkbox state
                    $('.status-toggle[data-id="' + id + '"]').prop('checked', !status);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update status.'
                    });
                }
            });
        });

        // Create Service Form Validation
        $('#createServiceForm').validate({
            rules: {
                category_id: {
                    required: true
                },
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 255
                },
                description: {
                    maxlength: 500
                },
                price: {
                    required: true,
                    min: 0
                },
                duration: {
                    required: true,
                    min: 1
                },
                image: {
                    extension: "jpg|jpeg|png|gif|webp"
                }
            },
            messages: {
                category_id: {
                    required: "Please select a category"
                },
                name: {
                    required: "Service name is required",
                    minlength: "Service name must be at least 2 characters",
                    maxlength: "Service name cannot exceed 255 characters"
                },
                description: {
                    maxlength: "Description cannot exceed 500 characters"
                },
                price: {
                    required: "Price is required",
                    min: "Price must be greater than or equal to 0"
                },
                duration: {
                    required: "Duration is required",
                    min: "Duration must be at least 1 minute"
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
                    url: '{{ route('admin.services.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#createServiceModal').modal('hide');
                        $('#createServiceForm')[0].reset();
                        $('#createServiceForm').find('.is-valid, .is-invalid')
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

        // Edit Service Form Validation
        $('#editServiceForm').validate({
            rules: {
                category_id: {
                    required: true
                },
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 255
                },
                description: {
                    maxlength: 500
                },
                price: {
                    required: true,
                    min: 0
                },
                duration: {
                    required: true,
                    min: 1
                },
                image: {
                    extension: "jpg|jpeg|png|gif|webp"
                }
            },
            messages: {
                category_id: {
                    required: "Please select a category"
                },
                name: {
                    required: "Service name is required",
                    minlength: "Service name must be at least 2 characters",
                    maxlength: "Service name cannot exceed 255 characters"
                },
                description: {
                    maxlength: "Description cannot exceed 500 characters"
                },
                price: {
                    required: "Price is required",
                    min: "Price must be greater than or equal to 0"
                },
                duration: {
                    required: "Duration is required",
                    min: "Duration must be at least 1 minute"
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
                var serviceId = $('#edit_service_id').val();

                $.ajax({
                    url: '{{ route('admin.services.update', '') }}/' + serviceId,
                    type: 'PUT',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#editServiceModal').modal('hide');
                        $('#editServiceForm')[0].reset();
                        $('#editServiceForm').find('.is-valid, .is-invalid')
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

        // Edit Service function
        window.editService = function(id) {
            $.ajax({
                url: '{{ route('admin.services.show', '') }}/' + id,
                type: 'GET',
                success: function(response) {
                    $('#edit_service_id').val(response.id);
                    $('#edit_category_id').val(response.category_id);
                    $('#edit_name').val(response.name);
                    $('#edit_description').val(response.description);
                    $('#edit_price').val(response.price);
                    $('#edit_duration').val(response.duration);
                    $('#edit_status').prop('checked', response.status == 1);
                    $('#editServiceModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to load service data.'
                    });
                }
            });
        };

        // Delete Service function
        window.deleteService = function(id) {
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
                        url: '{{ route('admin.services.destroy', '') }}/' + id,
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
                                text: 'Failed to delete service.'
                            });
                        }
                    });
                }
            });
        };

        // Prevent default form submission
        $('#createServiceForm, #editServiceForm').on('submit', function(e) {
            e.preventDefault();
        });
        });
    </script>
@endsection
