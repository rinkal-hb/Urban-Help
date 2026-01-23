@extends('layouts.master')

@section('styles')
 
      
@endsection

@section('content')

                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                        <h1 class="page-title fw-semibold fs-18 mb-0">Bar Charts</h1>
                        <div class="ms-md-1 ms-0">
                            <nav>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Apex Charts</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Bar Charts</li>
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
                                    <div class="card-title">Basic Bar Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-basic"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Grouped Bar Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-group"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Stacked Bar Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-stacked"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">100% Stacked Bar Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-full"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Bar Chart With Negative Values</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-negative"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Bar Chart With Markers</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-markers"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Reversed Bar Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-reversed"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Bar With Categogry DataLabels</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-categories"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Patterned Bar Chart</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-pattern"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Bar With Image Fill</div>
                                </div>
                                <div class="card-body">
                                    <div id="bar-image"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End::row-1 -->

                </div>

@endsection

@section('scripts')

        <!-- APEX CHARTS JS  -->
        <script src="{{asset('build/assets/libs/apexcharts/apexcharts.min.js')}}"></script>

        <!-- INTERNAL APEX BAR CHART JS  -->
        @vite('resources/assets/js/apexcharts-bar.js')


@endsection