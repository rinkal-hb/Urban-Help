<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="horizontal" data-nav-style="menu-click" data-menu-position="fixed" data-theme-mode="light">

    <head>

        <!-- Meta Data -->
		<meta charset="UTF-8">
        <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="Description" content="Laravel Bootstrap Responsive Admin Web Dashboard Template">
        <meta name="Author" content="Spruko Technologies Private Limited">
        <meta name="keywords" content="dashboard bootstrap, laravel template, admin panel in laravel, php admin panel, admin panel for laravel, admin template bootstrap 5, laravel admin panel, admin dashboard template, hrm dashboard, vite laravel, admin dashboard, ecommerce admin dashboard, dashboard laravel, analytics dashboard, template dashboard, admin panel template, bootstrap admin panel template">

        <!-- TITLE -->
		<title> YNEX - Laravel Bootstrap 5 Premium Admin & Dashboard Template </title>

        <!-- FAVICON -->
        <link rel="icon" href="{{asset('build/assets/images/brand-logos/favicon.ico')}}" type="image/x-icon">

        <!-- BOOTSTRAP CSS -->
	    <link  id="style" href="{{asset('build/assets/libs/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

        <!-- ICONS CSS -->
        <link href="{{asset('build/assets/icon-fonts/icons.css')}}" rel="stylesheet">
        
        <!-- APP SCSS -->
        @vite(['resources/sass/app.scss'])


        @include('layouts.components.landing.styles')


        @yield('styles')

	</head>

    <body class="landing-body jobs-landing">

        <!-- SWITCHER -->

        @include('layouts.components.landing.switcher')

        <!-- END SWITCHER -->

        <!-- PAGE -->
		<div class="landing-page-wrapper">

            <!-- HEADER -->

            @include('layouts.components.landing.jobs-header')

            <!-- END HEADER -->

            <!-- SIDEBAR -->

            @include('layouts.components.landing.jobs-sidebar')

            <!-- END SIDEBAR -->

            <!-- MAIN-CONTENT -->

            <div class="main-content landing-main">

                @yield('content')

                    <!-- FOOTER -->
                    @include('layouts.components.landing.jobs-footer')

                    <!-- FOOTER -->

            </div> 
            <!-- END MAIN-CONTENT -->
            
		</div>
        <!-- END PAGE-->

        <!-- SCRIPTS -->

        @include('layouts.components.landing.scripts')

        @yield('scripts')
        
        <!-- STICKY JS -->
		<script src="{{asset('build/assets/sticky.js')}}"></script>

        <!-- END SCRIPTS -->

	</body>
</html>
