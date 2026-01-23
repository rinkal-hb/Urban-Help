@extends('layouts.master')

@section('styles')
 
        <!-- LEAFLET MAPS CSS -->
        <link rel="stylesheet" href="{{asset('build/assets/libs/leaflet/leaflet.css')}}">
      
@endsection

@section('content')

                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                        <h1 class="page-title fw-semibold fs-18 mb-0">Leaflet Maps</h1>
                        <div class="ms-md-1 ms-0">
                            <nav>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Maps</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Leaflet Maps</li>
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
                                    <div class="card-title">Leaflet Map</div>
                                </div>
                                <div class="card-body">
                                    <div id="map"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Map With Markers,Circles and Polygons</div>
                                </div>
                                <div class="card-body">
                                    <div id="map1"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Map With Popup</div>
                                </div>
                                <div class="card-body">
                                    <div id="map-popup"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Map With Custom Icon</div>
                                </div>
                                <div class="card-body">
                                    <div id="map-custom-icon"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Interactive Choropleth Map</div>
                                </div>
                                <div class="card-body">
                                    <div id="interactive-map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End::row -->

                </div>

@endsection

@section('scripts')

        <!-- LEAFLET MAPS JS -->
        <script src="{{asset('build/assets/libs/leaflet/leaflet.js')}}"></script>
        @vite('resources/assets/js/leaflet.js')


@endsection