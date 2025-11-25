@extends('_layouts.base')

@section('layout-content')
    <div class="page">
        @include('_partials.navbar')

        @include('_partials.sidebar')

        <!-- Start::breadcrumb -->
        <div class="d-flex flex-column justify-content-center page-header-breadcrumb">
            <h4 id="page-title" class="fw-medium mb-2">@yield('title')</h4>
            <nav id="breadcrumb-container">
                @include('_partials.breadcrumb')
            </nav>
        </div>
        <!-- End::breadcrumb -->

        <div class="main-content app-content z-1">
            <div id="page-content" class="container-fluid">
                @yield('content')
            </div>
        </div>

        @include('_partials.copyright')
    </div>

    <div class="scrollToTop">
        <a href="javascript:void(0);" class="arrow">
            <i class="las la-angle-double-up fs-20 text-fixed-white"></i>
        </a>
    </div>

    <div id="responsive-overlay"></div>
@endsection

@push('scripts')
    <!-- Defaultmenu JS -->
    <script src="{{ asset('templates/js/defaultmenu.min.js') }}" data-partial="1"></script>
@endpush
