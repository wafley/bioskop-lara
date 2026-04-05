<!DOCTYPE html>
<html lang="en" data-theme-mode="light" data-menu-styles="light" style="--primary-rgb: 20, 206, 162;" data-default-header-styles="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name') }}</title>
    <meta name="Description" content="" />
    <meta name="Author" content="" />
    <meta name="keywords" content="" />

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />

    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('templates/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- Style Css -->
    <link href="{{ asset('templates/css/styles.min.css') }}" rel="stylesheet" />

    <!-- Icons Css -->
    <link href="{{ asset('templates/css/icons.css') }}" rel="stylesheet" />

    <!-- Sweetalert Css -->
    <link rel="stylesheet" href="{{ asset('templates/libs/sweetalert2/sweetalert2.min.css') }}">

    <style>
        .page {
            background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20'><rect width='20' height='20' fill='none' stroke='rgba(0,0,0,0.05)' stroke-width='1'/></svg>");
        }
    </style>

    @yield('styles')
</head>

<body>
    <!-- Loader -->
    <div id="loader">
        <img src="{{ asset('assets/images/loader.svg') }}" alt="Loader" />
    </div>
    <!-- Loader -->

    @yield('layout-content')

    <div id="modal-container">
        @yield('modal')
    </div>

    <!-- Main Theme Js -->
    <script src="{{ asset('templates/js/main.js') }}"></script>

    <!-- JQuery JS -->
    <script src="{{ asset('templates/libs/jquery/dist/jquery.min.js') }}"></script>

    <!-- Date & Time Picker JS -->
    <script src="{{ asset('templates/libs/moment/moment.js') }}"></script>

    <!-- Popper JS -->
    <script src="{{ asset('templates/libs/@popperjs/core/umd/popper.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('templates/libs/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Sweetalert JS -->
    <script src="{{ asset('templates/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    @stack('scripts')

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/spa.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script>
        window.appName = @json(config('app.name'));
        window.routes = {
            login: @json(route('login')),
        }
    </script>

    <script>
        let navHeight = $(".navbar").outerHeight() + 16;
        $(".main-content").css("margin-top", navHeight + "px");

        let footerHeight = $(".footer").outerHeight() + 16;
        $(".main-content").css("margin-bottom", footerHeight + "px");
    </script>

    <script>
        let date = moment(new Date());
        $("#year").text(date.format("YYYY"));
    </script>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
        @method('POST')
    </form>

    <script>
        $(document).on("click", "#logout-btn", function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Apakah Anda yakin ingin keluar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#adb5bd",
                confirmButtonText: "Ya, Keluar",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#logout-form").submit();
                }
            });
        });
    </script>

    <script>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                showToast('error', '{{ $error }}', 5000);
            @endforeach
        @endif

        @if (session('success'))
            showToast('success', '{{ session('success') }}', 3000);
        @endif

        @if (session('error'))
            showToast('error', '{{ session('error') }}', 3000);
        @endif
    </script>
    @yield('scripts')
</body>

</html>
