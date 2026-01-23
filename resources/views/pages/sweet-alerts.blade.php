@extends('layouts.master')

@section('styles')
 
        <!-- SWEETALERTS CSS -->
        <link rel="stylesheet" href="{{asset('build/assets/libs/sweetalert2/sweetalert2.min.css')}}">

@endsection

@section('content')

                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                        <h1 class="page-title fw-semibold fs-18 mb-0">Sweet Alerts</h1>
                        <div class="ms-md-1 ms-0">
                            <nav>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Apps</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Sweet Alerts</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Page Header Close -->

                    <!-- Start:: row-1 -->
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Basic Alert
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="basic-alert">Basic Alert</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Title With Text Under
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-text">Title With Text</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        With Text,Error Icon & Footer
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-footer">Alert Footer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End:: row-1 -->

                    <!-- Start:: row-2 -->
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Alert With Long Window
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="long-window">Long Window Here</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Custom HTML Description
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-description">Custom HTML Alert</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Alert With Multiple Buttons
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="three-buttons">Multiple Buttons</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End:: row-2 -->

                    <!-- Start:: row-3 -->
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Custom Positioned Dialog Alert
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-dialog">Alert Dialog</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Confirm Alert
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-confirm">Confirm Alert</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Alert With Parameters
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-parameter">Alert Parameters</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End:: row-3 -->

                    <!-- Start:: row-4 -->
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Alert With Image
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-image">Image Alert</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Alert With Image
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-custom-bg">Custom Alert</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Auto Close Alert
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-auto-close">Auto Close</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Ajax Request Alert
                                    </div>
                                </div>
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" id="alert-ajax">Ajax Request</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End:: row-4 -->

                </div>

@endsection

@section('scripts')

        <!-- SWEETALERTS JS -->
        <script src="{{asset('build/assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
        @vite('resources/assets/js/sweet-alerts.js')
        

@endsection