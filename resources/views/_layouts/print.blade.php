<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title') - {{ config('app.name') }}</title>
    <meta name="Description" content="" />
    <meta name="Author" content="" />
    <meta name="keywords" content="" />

    @stack('styles')
</head>

<body>
    @yield('content')

    @stack('scripts')
</body>

</html>
