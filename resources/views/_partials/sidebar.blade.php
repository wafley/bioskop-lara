<!-- Start::sidebar -->
<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header">
        <a href="{{ url('/') }}" class="header-logo">
            <img src="{{ asset('assets/images/brand-logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
            <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" alt="logo" class="toggle-logo">
            <img src="{{ asset('assets/images/brand-logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
            <img src="{{ asset('assets/images/brand-logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>

            <ul class="main-menu">
                @php
                    function isActive($route)
                    {
                        return request()->routeIs($route) ? 'active' : '';
                    }
                @endphp

                <!-- Start::slide -->
                <li class="slide">
                    <a href="{{ route('dashboard.index') }}" class="side-menu__item {{ isActive('dashboard.index') }} spa-link">
                        <span class="side-menu__icon">
                            <i class='fe fe-grid'></i>
                        </span>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
                <!-- End::slide -->

                @role('admin')
                    <!-- Start::slide -->
                    <li class="slide">
                        <a href="{{ route('operators.index') }}" class="side-menu__item {{ isActive('operators.*') }} spa-link">
                            <span class="side-menu__icon">
                                <i class='fe fe-users'></i>
                            </span>
                            <span class="side-menu__label">Operators</span>
                        </a>
                    </li>
                    <!-- End::slide -->

                    <!-- Start::slide -->
                    <li class="slide">
                        <a href="{{ route('movies.index') }}" class="side-menu__item {{ isActive('movies.*') }} spa-link">
                            <span class="side-menu__icon">
                                <i class='fe fe-film'></i>
                            </span>
                            <span class="side-menu__label">Movies</span>
                        </a>
                    </li>
                    <!-- End::slide -->

                    <!-- Start::slide -->
                    <li class="slide">
                        <a href="{{ route('studios.index') }}" class="side-menu__item {{ isActive('studios.*') }} spa-link">
                            <span class="side-menu__icon">
                                <i class='fe fe-airplay'></i>
                            </span>
                            <span class="side-menu__label">Studios</span>
                        </a>
                    </li>
                    <!-- End::slide -->

                    <!-- Start::slide -->
                    <li class="slide">
                        <a href="{{ route('schedules.index') }}" class="side-menu__item {{ isActive('schedules.*') }} spa-link">
                            <span class="side-menu__icon">
                                <i class='fe fe-calendar'></i>
                            </span>
                            <span class="side-menu__label">Schedules</span>
                        </a>
                    </li>
                    <!-- End::slide -->
                @endrole
            </ul>

            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg>
            </div>
        </nav>
    </div>
</aside>
<!-- End::sidebar -->
