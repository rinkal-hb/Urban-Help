@extends('admin.layouts.custom-master')

@section('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/libs/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/libs/swiper/swiper-bundle.min.css') }}">
@endsection

@section('content')
@section('error-body')

    <body class="bg-white">
    @endsection

    <div class="row authentication mx-0">
        <div class="col-xxl-7 col-xl-7 col-lg-12">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-xxl-6 col-xl-7 col-lg-7 col-md-7 col-sm-8 col-12">
                    <div class="p-5">
                        <div class="mb-3">
                            <a href="{{ route('home.login') }}">
                                <img src="{{ asset('assets/img/logo/logo.png') }}" alt=""
                                    class="authentication-brand desktop-logo"
                                    style="height: 4rem !important; width: auto; margin: 0 auto;">
                                <img src="{{ asset('assets/img/logo/urban_help_fav.png') }}" alt=""
                                    class="authentication-brand desktop-dark">
                            </a>
                        </div>
                        <p class="h5 fw-semibold mb-2">Sign In</p>
                        <p class="mb-3 text-muted op-7 fw-normal">Welcome back!</p>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                            @csrf
                            <div class="row gy-3">
                                <div class="col-xl-12 mt-0">
                                    <label for="email" class="form-label text-default">Email</label>
                                    <input type="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}"
                                        placeholder="Enter your email" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-12 mb-3">
                                    <label for="password" class="form-label text-default">Password</label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Enter your password" required>
                                        <button class="btn btn-light" type="button"
                                            onclick="createpassword('password',this)">
                                            <i class="ri-eye-off-line align-middle"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember" value="1">
                                            <label class="form-check-label text-muted fw-normal" for="remember">
                                                Remember me
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 d-grid mt-2">
                                    <button type="submit" class="btn btn-lg btn-primary">Sign In</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-5 col-xl-5 col-lg-5 d-xl-block d-none px-0">
            <div class="bg-primary bg-opacity-10 h-100 d-flex align-items-center justify-content-center">
                <div class="text-center p-5">
                    <div class="mb-4">
                        <img src="{{ asset('assets/img/logo/urban_help_fav.png') }}" width="80" alt="Urban Help">
                    </div>
                    <h5 class="text-primary mb-2">Urban Help</h5>
                    <p class="text-muted">Your trusted platform for urban services</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('build/assets/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('build/assets/show-password.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    }
                },
                messages: {
                    email: {
                        required: "Email is required",
                        email: "Please enter a valid email address"
                    },
                    password: {
                        required: "Password is required",
                        minlength: "Password must be at least 6 characters"
                    }
                },
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.col-xl-12').append(error);
                }
            });
        });
    </script>
@endsection
