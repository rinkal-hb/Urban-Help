@extends('admin.layouts.master')

@section('title', 'Payments')

@section('content')
<div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Payments Management</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payments</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-dollar fs-48 text-muted mb-3"></i>
                    <h4>Payments Management</h4>
                    <p class="text-muted">This module will be implemented to manage payments and transactions.</p>
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Payments module is ready for implementation. This will include transaction tracking, refunds, and commission management.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection