@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Route;

    if (!function_exists('breadcrumbLabel')) {
        function breadcrumbLabel($segment)
        {
            return match ($segment) {
                'create' => 'Tambah',
                'edit' => 'Ubah',
                'show' => 'Detail',
                default => Str::headline($segment),
            };
        }
    }

    $currentRoute = Route::current();
    $routeName = $currentRoute ? $currentRoute->getName() : '';
    $segments = $routeName ? explode('.', $routeName) : [];

    $breadcrumb = [];
    $urlSegments = [];

    foreach ($segments as $index => $segment) {
        if ($segment === 'index') {
            continue;
        }

        $urlSegments[] = $segment;
        $routePrefix = implode('.', $urlSegments);

        $routeUrl = 'javascript:void(0);';

        if (Route::has($routePrefix . '.index')) {
            $routeUrl = route($routePrefix . '.index');
        } elseif (Route::has($routePrefix)) {
            $route = Route::getRoutes()->getByName($routePrefix);
            if ($route && count($route->parameterNames()) === 0) {
                $routeUrl = route($routePrefix);
            }
        }

        $breadcrumb[] = [
            'label' => breadcrumbLabel($segment),
            'url' => $routeUrl,
        ];
    }
@endphp

<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('/') }}">{{ config('app.name') }}</a>
    </li>

    @foreach ($breadcrumb as $item)
        @if (!$loop->last)
            <li class="breadcrumb-item">
                <a href="{{ $item['url'] }}" class="spa-link">{{ $item['label'] }}</a>
            </li>
        @else
            <li class="breadcrumb-item active fw-normal" aria-current="page">
                {{ $item['label'] }}
            </li>
        @endif
    @endforeach
</ol>
