@extends('layouts.master')

@section('styles')
 
      
@endsection

@section('content')

                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                        <h1 class="page-title fw-semibold fs-18 mb-0">Products List</h1>
                        <div class="ms-md-1 ms-0">
                            <nav>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Ecommerce</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Products List</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Page Header Close -->

                    <!-- Start::row -->
                    <div class="row">   
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Products List
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive mb-4">
                                        <table class="table text-nowrap table-bordered">
                                            <thead>
                                                <tr>
                                                    <th scope="col">
                                                        <input class="form-check-input check-all" type="checkbox" id="all-products" value="" aria-label="...">
                                                    </th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Category</th>
                                                    <th scope="col">Price</th>
                                                    <th scope="col">Stock</th>
                                                    <th scope="col">Gender</th>
                                                    <th scope="col">Seller</th>
                                                    <th scope="col">Published</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="product-list">
                                                    <td class="product-checkbox"><input class="form-check-input" type="checkbox" id="product1" value="" aria-label="..."></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-md avatar-rounded">
                                                                    <img src="{{asset('build/assets/images/ecommerce/png/1.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold">
                                                                DapZem & Co Blue Hoodie
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-default">Clothing</span>
                                                    </td>
                                                    <td>$1,299</td>
                                                    <td>283</td>
                                                    <td>Male</td>
                                                    <td>Apilla.co.in</td>
                                                    <td>24,Nov 2022 - 04:42PM</td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="{{url('edit-products')}} " class="btn btn-icon btn-sm btn-info-light"><i class="ri-edit-line"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger-light product-btn"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="product-list">
                                                    <td class="product-checkbox"><input class="form-check-input" type="checkbox" id="product2" value="" aria-label="..."></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-md avatar-rounded">
                                                                    <img src="{{asset('build/assets/images/ecommerce/png/2.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold">
                                                                Leather jacket for men
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-default">Clothing</span>
                                                    </td>
                                                    <td>$799</td>
                                                    <td>98</td>
                                                    <td>Male</td>
                                                    <td>Donzo Company</td>
                                                    <td>18,Nov 2022 - 06:53AM</td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="{{url('edit-products')}} " class="btn btn-icon btn-sm btn-info-light"><i class="ri-edit-line"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger-light product-btn"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="product-list">
                                                    <td class="product-checkbox"><input class="form-check-input" type="checkbox" id="product3" value="" aria-label="..."></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-md avatar-rounded">
                                                                    <img src="{{asset('build/assets/images/ecommerce/png/15.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold">
                                                                Orange Smart Watch
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-default">Watches</span>
                                                    </td>
                                                    <td>$349</td>
                                                    <td>1,293</td>
                                                    <td>Male,Female</td>
                                                    <td>SlowTrack Company</td>
                                                    <td>21,Oct 2022 - 11:36AM</td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="{{url('edit-products')}} " class="btn btn-icon btn-sm btn-info-light"><i class="ri-edit-line"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger-light product-btn"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="product-list">
                                                    <td class="product-checkbox"><input class="form-check-input" type="checkbox" id="product4" value="" aria-label="..."></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-md avatar-rounded">
                                                                    <img src="{{asset('build/assets/images/ecommerce/png/3.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold">
                                                                Winter Coat For Women
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-default">Clothing</span>
                                                    </td>
                                                    <td>$189</td>
                                                    <td>322</td>
                                                    <td>Female</td>
                                                    <td>WoodHill.co.in</td>
                                                    <td>16,Oct 2022 - 12:45AM</td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="{{url('edit-products')}} " class="btn btn-icon btn-sm btn-info-light"><i class="ri-edit-line"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger-light product-btn"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="product-list">
                                                    <td class="product-checkbox"><input class="form-check-input" type="checkbox" id="product5" value="" aria-label="..."></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-md avatar-rounded">
                                                                    <img src="{{asset('build/assets/images/ecommerce/png/4.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold">
                                                                Vintage White Full Sleeve Tee-Shirt
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-default">Clothing</span>
                                                    </td>
                                                    <td>$2,499</td>
                                                    <td>194</td>
                                                    <td>Male,Female</td>
                                                    <td>Watches.co.in</td>
                                                    <td>12,Aug 2022 - 11:21AM</td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="{{url('edit-products')}} " class="btn btn-icon btn-sm btn-info-light"><i class="ri-edit-line"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger-light product-btn"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="product-list">
                                                    <td class="product-checkbox"><input class="form-check-input" type="checkbox" id="product6" value="" aria-label="..."></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-md avatar-rounded">
                                                                    <img src="{{asset('build/assets/images/ecommerce/png/13.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold">
                                                                Orange Watch series (44mm)
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-default">Watches</span>
                                                    </td>
                                                    <td>$899</td>
                                                    <td>267</td>
                                                    <td>Male,Female</td>
                                                    <td>Watches.co.in</td>
                                                    <td>05,Sep 2022 - 10:14AM</td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="{{url('edit-products')}} " class="btn btn-icon btn-sm btn-info-light"><i class="ri-edit-line"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger-light product-btn"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="product-list">
                                                    <td class="product-checkbox"><input class="form-check-input" type="checkbox" id="product7" value="" aria-label="..."></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-md avatar-rounded">
                                                                    <img src="{{asset('build/assets/images/ecommerce/png/12.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold">
                                                                Sweater For Women
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-default">Clothing</span>
                                                    </td>
                                                    <td>$499</td>
                                                    <td>143</td>
                                                    <td>Female</td>
                                                    <td>Louie Philippe</td>
                                                    <td>18,Nov 2022 - 14:35PM</td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="{{url('edit-products')}} " class="btn btn-icon btn-sm btn-info-light"><i class="ri-edit-line"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger-light product-btn"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="product-list">
                                                    <td class="product-checkbox"><input class="form-check-input" type="checkbox" id="product8" value="" aria-label="..."></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-md avatar-rounded">
                                                                    <img src="{{asset('build/assets/images/ecommerce/png/16.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold">
                                                                Ikonic Smart Watch(40mm)
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-default">Watches</span>
                                                    </td>
                                                    <td>$999</td>
                                                    <td>365</td>
                                                    <td>Female</td>
                                                    <td>Kohino.zaps.com</td>
                                                    <td>27,Nov 2022 - 05:12AM</td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="{{url('edit-products')}} " class="btn btn-icon btn-sm btn-info-light"><i class="ri-edit-line"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger-light product-btn"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="product-list">
                                                    <td class="product-checkbox"><input class="form-check-input" type="checkbox" id="product9" value="" aria-label="..."></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-md avatar-rounded">
                                                                    <img src="{{asset('build/assets/images/ecommerce/png/23.png')}}" alt="">
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold">
                                                                Apple Watch Series 5
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-default">Watches</span>
                                                    </td>
                                                    <td>$1,499</td>
                                                    <td>257</td>
                                                    <td>Male,Female</td>
                                                    <td>Apple Corporation</td>
                                                    <td>29,Nov 2022 - 16:32PM</td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="{{url('edit-products')}} " class="btn btn-icon btn-sm btn-info-light"><i class="ri-edit-line"></i></a>
                                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger-light product-btn"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <nav aria-label="...">
                                            <ul class="pagination mb-0">
                                                <li class="page-item disabled">
                                                    <span class="page-link">Previous</span>
                                                </li>
                                                <li class="page-item"><a class="page-link" href="javascript:void(0);">1</a></li>
                                                <li class="page-item active" aria-current="page">
                                                    <span class="page-link">2</span>
                                                </li>
                                                <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                                                <li class="page-item">
                                                    <a class="page-link" href="javascript:void(0);">Next</a>
                                                </li>
                                            </ul>
                                        </nav>
                                        <button class="btn btn-danger btn-wave m-1">Delete All</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End::row -->

                </div>

@endsection

@section('scripts')

        <!-- INTERNAL PRODUCT DETAILS JS -->
        @vite('resources/assets/js/product-list.js')


@endsection