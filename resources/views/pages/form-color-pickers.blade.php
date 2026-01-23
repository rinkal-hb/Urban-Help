@extends('layouts.master')

@section('styles')
 
        <!-- PRISM CSS -->
        <link rel="stylesheet" href="{{asset('build/assets/libs/prismjs/themes/prism-coy.min.css')}}">

        <!--SIMONWEP CSS -->
        <link rel="stylesheet" href="{{asset('build/assets/libs/@simonwep/pickr/themes/classic.min.css')}}">
        <link rel="stylesheet" href="{{asset('build/assets/libs/@simonwep/pickr/themes/monolith.min.css')}}">
        <link rel="stylesheet" href="{{asset('build/assets/libs/@simonwep/pickr/themes/nano.min.css')}}">
      
@endsection

@section('content')

            <div class="container-fluid">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Color Pickers</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Form Elements</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Color Pickers</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Page Header Close -->

                <!-- Start::row -->
                <div class="row">
                    <div class="col-xl-3">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    Bootstrap Color Picker
                                </div>
                                <div class="prism-toggle">
                                    <button class="btn btn-sm btn-primary-light">Show Code<i class="ri-code-line ms-2 d-inline-block align-middle"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="color" class="form-control form-control-color border-0"
                                        id="exampleColorInput" value="#136ad0" title="Choose your color">
                            </div>
                            <div class="card-footer d-none border-top-0">
<!-- Prism Code -->
<pre class="language-html"><code class="language-html">&lt;input type="color" class="form-control form-control-color border-0"
    id="exampleColorInput" value="#136ad0" title="Choose your color"&gt;</code></pre>
<!-- Prism Code -->
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    Classic
                                </div>
                            </div>
                            <div class="card-body d-flex justify-content-between">
                                <div>
                                    <div class="theme-container"></div>
                                    <div class="pickr-container example-picker"></div>
                                </div>
                                <div>
                                    <div class="theme-container1"></div>
                                    <div class="pickr-container1 example-picker"></div>
                                </div>
                                <div>
                                    <div class="theme-container2"></div>
                                    <div class="pickr-container2 example-picker"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End::row -->

            </div>

@endsection

@section('scripts')

        <!-- PRISM JS -->
        <script src="{{asset('build/assets/libs/prismjs/prism.js')}}"></script>
        @vite('resources/assets/js/prism-custom.js')

        <!-- COLOR PICKER JS -->
        @vite('resources/assets/js/color-picker.js')


@endsection