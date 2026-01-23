@extends('layouts.master')

@section('styles')
 
      
@endsection

@section('content')

                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                        <h1 class="page-title fw-semibold fs-18 mb-0">Pie Charts</h1>
                        <div class="ms-md-1 ms-0">
                            <nav>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Apex Charts</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Pie Charts</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Page Header Close -->

                    <!-- Start::row-1 -->
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Basic Pie Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="pie-basic"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Simple Donut Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="donut-simple"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Updating Donut Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="donut-update"></div>
                                    <div class="text-center mt-4">
                                        <button class="btn btn-primary btn-sm m-1" id="randomize">Randomize</button>
                                        <button class="btn btn-primary btn-sm m-1" id="add">Add</button>
                                        <button class="btn btn-primary btn-sm m-1" id="remove">Remove</button>
                                        <button class="btn btn-primary btn-sm m-1" id="reset">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Monochrome Pie Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="pie-monochrome"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Gradient Donut Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="donut-gradient"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Donut Chart With Patterns</div>
                                </div>
                                <div class="card-body">
                                    <div id="donut-pattern"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Image Filled Pie Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="pie-image"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End::row-1 -->
                    
                </div>

@endsection

@section('scripts')

        <!-- APEX CHARTS JS -->
        <script src="{{asset('build/assets/libs/apexcharts/apexcharts.min.js')}}"></script>

        <!-- INTERNAL APEX PIE CHARTS JS -->
        @vite('resources/assets/js/apexcharts-pie.js')
        

@endsection