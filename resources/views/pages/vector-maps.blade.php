@extends('layouts.master')

@section('styles')
 
        <!-- JSVECTOR MAPS CSS -->
        <link rel="stylesheet" href="{{asset('build/assets/libs/jsvectormap/css/jsvectormap.min.css')}}">

@endsection

@section('content')

                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                        <h1 class="page-title fw-semibold fs-18 mb-0">Vector Maps</h1>
                        <div class="ms-md-1 ms-0">
                            <nav>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Maps</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Vector Maps</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Page Header Close -->

                    <!-- Start::row -->
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Basic Vector Map</div>
                                </div>
                                <div class="card-body">
                                    <div id="vector-map"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Map With Markers</div>
                                </div>
                                <div class="card-body">
                                    <div id="marker-map"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Map With Image Markers</div>
                                </div>
                                <div class="card-body">
                                    <div id="marker-image-map"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Map With Lines</div>
                                </div>
                                <div class="card-body">
                                    <div id="lines-map"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">US Vector Map</div>
                                </div>
                                <div class="card-body">
                                    <div id="us-map"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Russia Vector Map</div>
                                </div>
                                <div class="card-body">
                                    <div id="russia-map"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Spain Vector Map</div>
                                </div>
                                <div class="card-body">
                                    <div id="spain-map"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Canada Vector Map</div>
                                </div>
                                <div class="card-body">
                                    <div id="canada-map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End::row -->

                </div>

@endsection

@section('scripts')

        <!-- JSVECTOR MAPS JS -->
        <script src="{{asset('build/assets/libs/jsvectormap/js/jsvectormap.min.js')}}"></script>

        <!-- JSVECTOR MAPS JS -->
        <script src="{{asset('build/assets/libs/jsvectormap/maps/world-merc.js')}}"></script>
        @vite('resources/assets/js/us-merc-en.js')


        @vite('resources/assets/js/russia.js')


        @vite('resources/assets/js/spain.js')


        @vite('resources/assets/js/canada.js')


        @vite('resources/assets/js/jsvectormap.js')


@endsection