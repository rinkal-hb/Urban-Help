@extends('admin.layouts.master')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Audit Logs</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Audit Logs</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-history fs-48 text-muted mb-3"></i>
                    <h4>Audit Logs</h4>
                    <p class="text-muted">This module will be implemented to track system activities.</p>
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Audit Logs module is ready for implementation. This will include activity tracking, security logs, and system monitoring.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection