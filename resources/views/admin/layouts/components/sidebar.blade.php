<aside class="app-sidebar sticky" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="header-logo">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="logo" class="desktop-logo">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="logo" class="toggle-logo">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="logo" class="desktop-dark">
            <img src="{{ asset('assets/img/logo/urban_help_fav.png') }}" alt="logo" class="toggle-dark">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="logo" class="desktop-white">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="logo" class="toggle-white">
        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll">

        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>
            {{-- <ul class="main-menu">
                <!-- Start::slide__category -->
                <li class="slide__category"><span class="category-name">Admin Panel</span></li>
                <!-- End::slide__category -->

                <!-- Start::slide -->
                <li class="slide">
                    <a href="{{ route('admin.dashboard') }}" class="side-menu__item">
                        <i class="bx bx-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
                <!-- End::slide -->

            </ul> --}}

            <ul class="main-menu">
                <!-- Dashboard -->
                <li class="slide">
                    <a href="{{ route('admin.dashboard') }}"
                        class="side-menu__item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bx bx-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <!-- User Management -->
                <li class="slide has-sub {{ request()->routeIs('admin.users.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="bx bx-user side-menu__icon"></i>
                        <span class="side-menu__label">User Management</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="{{ route('admin.users.index') }}"
                                class="side-menu__item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">All
                                Users</a>
                        </li>

                    </ul>
                </li>

                <!-- Role Management -->
                {{-- @can('roles.read.all') --}}
                <li class="slide has-sub {{ request()->routeIs('admin.roles.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="bx bx-shield side-menu__icon"></i>
                        <span class="side-menu__label">Role Management</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="{{ route('admin.roles.index') }}"
                                class="side-menu__item {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}">All
                                Roles</a>
                        </li>
                        @can('roles.manage.all')
                            <li class="slide">
                                <a href="{{ route('admin.roles.createrole') }}"
                                    class="side-menu__item {{ request()->routeIs('admin.roles.createrole') ? 'active' : '' }}">Add
                                    Role</a>
                            </li>
                        @endcan
                    </ul>
                </li>
                {{-- @endcan --}}

                <!-- Permission Management -->
                {{-- @can('permissions.read.all') --}}
                <li class="slide has-sub {{ request()->routeIs('admin.permissions.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="bx bx-key side-menu__icon"></i>
                        <span class="side-menu__label">Permissions</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="{{ route('admin.permissions.index') }}"
                                class="side-menu__item {{ request()->routeIs('admin.permissions.index') ? 'active' : '' }}">All
                                Permissions</a>
                        </li>
                        {{-- @can('permissions.manage.all') --}}
                        <li class="slide">
                            <a href="{{ route('admin.permissions.createpermission') }}"
                                class="side-menu__item {{ request()->routeIs('admin.permissions.createpermission') ? 'active' : '' }}">Add
                                Permission</a>
                        </li>
                        {{-- @endcan --}}
                    </ul>
                </li>
                {{-- @endcan --}}

                {{-- <!-- System Settings -->
                @if (auth()->user()->hasRole('super_admin'))
                    <li
                        class="slide has-sub {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.audit-logs.*') ? 'open' : '' }}">
                        <a href="javascript:void(0);" class="side-menu__item">
                            <i class="bx bx-cog side-menu__icon"></i>
                            <span class="side-menu__label">System</span>
                            <i class="fe fe-chevron-right side-menu__angle"></i>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide">
                                <a href="{{ route('admin.settings.index') }}"
                                    class="side-menu__item {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">Settings</a>
                            </li>
                            <li class="slide">
                                <a href="{{ route('admin.audit-logs.index') }}"
                                    class="side-menu__item {{ request()->routeIs('admin.audit-logs.index') ? 'active' : '' }}">Audit
                                    Logs</a>
                            </li>
                        </ul>
                    </li>
                @endif --}}

                <!-- Services & Categories -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="bx bx-briefcase side-menu__icon"></i>
                        <span class="side-menu__label">Services</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide">
                            <a href="{{ route('admin.categories.index') }}" class="side-menu__item">Categories</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('admin.services.index') }}" class="side-menu__item">Services</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                    width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg></div>
        </nav>
        <!-- End::nav -->

    </div>
    <!-- End::main-sidebar -->

</aside>
