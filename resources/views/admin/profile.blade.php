@extends('admin.layouts.master')

@section('title', 'Profile')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Profile</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if (auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" alt="Profile"
                                    class="avatar avatar-xxl rounded-circle mb-3">
                            @else
                                <span
                                    class="avatar avatar-xxl rounded-circle bg-primary mb-3 d-inline-flex align-items-center justify-content-center">
                                    <span class="fs-24 fw-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </span>
                            @endif
                        </div>
                        <h5 class="fw-semibold mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-muted mb-3">{{ auth()->user()->getRoleNames()->implode(', ') }}</p>
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            @foreach (auth()->user()->roles as $role)
                                <span class="badge bg-primary">{{ $role->display_name }}</span>
                            @endforeach
                        </div>
                        <button class="btn btn-primary" onclick="editProfile()">
                            <i class="ri-edit-line me-1"></i>Edit Profile
                        </button>
                    </div>
                </div>

                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Account Information</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="mb-0">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Phone</label>
                            <p class="mb-0">{{ auth()->user()->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Member Since</label>
                            <p class="mb-0">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-muted">Last Login</label>
                            <p class="mb-0">
                                {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Never' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Personal Information</div>
                    </div>
                    <div class="card-body">
                        <form id="profileForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name"
                                            value="{{ auth()->user()->name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email"
                                            value="{{ auth()->user()->email }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="phone"
                                            value="{{ auth()->user()->phone }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth"
                                            value="{{ auth()->user()->date_of_birth }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" id="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male"
                                                {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female"
                                                {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other"
                                                {{ auth()->user()->gender == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Profile Picture</label>
                                        <input type="file" class="form-control" id="avatar" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" id="address" rows="3">{{ auth()->user()->address }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" id="city"
                                            value="{{ auth()->user()->city }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control" id="state"
                                            value="{{ auth()->user()->state }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" id="postal_code"
                                            value="{{ auth()->user()->postal_code }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country"
                                            value="{{ auth()->user()->country }}">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Change Password</div>
                    </div>
                    <div class="card-body">
                        <form id="passwordForm">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-warning">
                                    <i class="ri-lock-line me-1"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>


        $(document).ready(function() {
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                updateProfile();
            });

            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();
                changePassword();
            });
        });

        function editProfile() {
            $('#profileForm input, #profileForm select, #profileForm textarea').prop('disabled', false);
        }

        function updateProfile() {
            const form = document.getElementById('profileForm');
            const formData = new FormData(form);

            // Add form fields to FormData
            formData.append('name', $('#name').val());
            formData.append('email', $('#email').val());
            formData.append('phone', $('#phone').val());
            formData.append('date_of_birth', $('#date_of_birth').val());
            formData.append('gender', $('#gender').val());
            formData.append('address', $('#address').val());
            formData.append('city', $('#city').val());
            formData.append('state', $('#state').val());
            formData.append('postal_code', $('#postal_code').val());
            formData.append('country', $('#country').val());
            formData.append('_token', '{{ csrf_token() }}');

            const avatarFile = $('#avatar')[0].files[0];
            if (avatarFile) {
                formData.append('avatar', avatarFile);
            }

            $.ajax({
                url: '{{ route('admin.profile.update') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    showAlert('Profile updated successfully', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Validation errors:\n';
                        Object.keys(errors).forEach(field => {
                            errorMessage += `${field}: ${errors[field].join(', ')}\n`;
                        });
                        showAlert(errorMessage, 'error');
                    } else {
                        showAlert('Error updating profile', 'error');
                    }
                }
            });
        }

        function changePassword() {
            const currentPassword = $('#current_password').val();
            const newPassword = $('#new_password').val();
            const confirmPassword = $('#confirm_password').val();

            if (newPassword !== confirmPassword) {
                showAlert('New passwords do not match', 'error');
                return;
            }

            $.ajax({
                url: '{{ route('admin.profile.change-password') }}',
                method: 'POST',
                data: {
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showAlert('Password changed successfully', 'success');
                    $('#passwordForm')[0].reset();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        Object.keys(errors).forEach(field => {
                            errorMessage += errors[field].join(', ') + '\n';
                        });
                        showAlert(errorMessage, 'error');
                    } else {
                        showAlert('Error changing password', 'error');
                    }
                }
            });
        }

        function showAlert(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;

            $('.container-fluid').prepend(alertHtml);

            setTimeout(() => {
                $('.alert').fadeOut();
            }, 5000);
        }
    </script>
@endsection
