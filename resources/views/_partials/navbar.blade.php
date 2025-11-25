<!-- Start::navbar -->
<header class="app-header">
    <div class="main-header-container container-fluid">
        <div class="header-content-left">
            <div class="header-element">
                <div class="horizontal-logo">
                    <a href="{{ url('/') }}" class="header-logo">
                        <img src="{{ asset('assets/images/brand-logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
                        <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" alt="logo" class="toggle-logo">
                        <img src="{{ asset('assets/images/brand-logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
                        <img src="{{ asset('assets/images/brand-logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
                    </a>
                </div>
            </div>

            <div class="header-element">
                <a aria-label="anchor" href="javascript:void(0);" class="sidemenu-toggle header-link" data-bs-toggle="sidebar">
                    <span class="open-toggle me-2">
                        <i class="fe fe-align-left header-link-icon border-0"></i>
                    </span>
                </a>
            </div>
        </div>

        <div class="header-content-right">
            <div class="header-element header-theme-mode">
                <a aria-label="anchor" href="javascript:void(0);" class="header-link layout-setting">
                    <i class="fe fe-sun bx-flip-horizontal header-link-icon ionicon  dark-layout"></i>
                    <i class="fe fe-moon bx-flip-horizontal header-link-icon ionicon light-layout"></i>
                </a>
            </div>

            <div class="header-element mainuserProfile">
                <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="d-sm-flex wd-100p lh-0">
                            <div class="avatar avatar-md">
                                <img alt="avatar" class="rounded-circle"
                                    src="{{ asset('assets/images/placeholders/profile-placeholder.jpg') }}">
                            </div>
                            <div class="ms-2 my-auto d-none d-xl-flex">
                                <h6 class=" font-weight-semibold mb-0 fs-13 user-name d-sm-block d-none">
                                    {{ Auth::user()->name ?? 'Admin' }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </a>

                <div class="main-header-dropdown dropdown-menu pt-0 border-0 header-profile-dropdown dropdown-menu-end dropdown-menu-arrow"
                    aria-labelledby="mainHeaderProfile">
                    <div class="p-3 menu-header-content text-fixed-white rounded-top text-center">
                        <div class="">
                            <div class="avatar avatar-xl rounded-circle">
                                <img alt="" class="rounded-circle"
                                    src="{{ asset('assets/images/placeholders/profile-placeholder.jpg') }}">
                            </div>
                            <p class="text-fixed-white fs-18 fw-semibold mb-0">
                                {{ Auth::user()->name ?? 'Admin' }}
                            </p>
                            <span class="fs-13 text-fixed-white">
                                {{ Auth::user()->role->name }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <hr class="dropdown-divider">
                    </div>
                    <div>
                        <a class="dropdown-item spa-link" href="{{ route('profile.index') }}">
                            <i class="fa fa-user me-1"></i>
                            Profile
                        </a>
                        <a class="dropdown-item text-danger" href="#!" id="logout-btn">
                            <i class="fa fa-sign-out-alt me-1"></i>
                            Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- End::navbar -->
