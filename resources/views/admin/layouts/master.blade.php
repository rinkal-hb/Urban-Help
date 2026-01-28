<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Laravel Bootstrap Responsive Admin Web Dashboard Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords"
        content="dashboard bootstrap, laravel template, admin panel in laravel, php admin panel, admin panel for laravel, admin template bootstrap 5, laravel admin panel, admin dashboard template, hrm dashboard, vite laravel, admin dashboard, ecommerce admin dashboard, dashboard laravel, analytics dashboard, template dashboard, admin panel template, bootstrap admin panel template">

    <!-- TITLE -->
    <title>{{ config('app.name') }}</title>

    <!-- FAVICON -->
    <link rel="icon" href="{{ asset('assets/img/logo/urban_help_fav.png') }}" type="image/x-icon">

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="{{ asset('build/assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- ICONS CSS -->
    <link href="{{ asset('build/assets/icon-fonts/icons.css') }}" rel="stylesheet">

    <!-- APP SCSS -->
    @vite(['resources/sass/app.scss'])

    @include('admin.layouts.components.styles')

    <!-- MAIN JS -->
    <script src="{{ asset('build/assets/main.js') }}"></script>

    @yield('styles')

</head>

<body>

    <!-- SWITCHER -->

    @include('admin.layouts.components.switcher')

    <!-- END SWITCHER -->

    <!-- LOADER -->
    <div id="loader">
        <img src="{{ asset('build/assets/images/media/loader.svg') }}" alt="">
    </div>
    <!-- END LOADER -->

    <!-- PAGE -->
    <div class="page">

        <!-- HEADER -->

        @include('admin.layouts.components.header')

        <!-- END HEADER -->

        <!-- SIDEBAR -->

        @include('admin.layouts.components.sidebar')

        <!-- END SIDEBAR -->

        <!-- MAIN-CONTENT -->

        <div class="main-content app-content">

            @yield('content')
            @yield('modal')
        </div>
        <!-- END MAIN-CONTENT -->

        <!-- SEARCH-MODAL -->

        @include('admin.layouts.components.search-modal')

        <!-- END SEARCH-MODAL -->

        <!-- FOOTER -->

        @include('admin.layouts.components.footer')

        <!-- END FOOTER -->

    </div>
    <!-- END PAGE-->

    <!-- SCRIPTS -->

    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- JQUERY VALIDATION -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <!-- SWEETALERT2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DATATABLES -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    @include('admin.layouts.components.scripts')

    @yield('scripts')

    <!-- STICKY JS -->
    <script src="{{ asset('build/assets/sticky.js') }}"></script>

    <!-- APP JS -->
    @vite('resources/js/app.js')

    <!-- CUSTOM-SWITCHER JS -->
    @vite('resources/assets/js/custom-switcher.js')

    <!-- END SCRIPTS -->

</body>

</html>
