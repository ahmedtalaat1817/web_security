<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Quickbite - Fast Food Delivery">

    <title>{{ config('app.name', 'Quickbite!') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css'])

    <style>
        html {
            font-size: 16px;
        }

        @media (max-width: 576px) {
            html {
                font-size: 14px;
            }
        }

        @media (min-width: 1400px) {
            html {
                font-size: 17px;
            }
        }

        body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        .container, .container-fluid {
            max-width: 100%;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .container-custom {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        @media (max-width: 768px) {
            .container-custom {
                padding: 0 0.75rem;
            }
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .main-content {
            min-height: calc(100vh - 200px);
        }

        @media (max-width: 576px) {
            .main-content {
                min-height: calc(100vh - 150px);
            }
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar')

    <main class="main-content">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot }}
        @endif
    </main>

    @include('layouts.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    var theme = localStorage.getItem('foodie_theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
})();
function toggleTheme() {
    var html = document.documentElement;
    var current = html.getAttribute('data-theme');
    var next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('foodie_theme', next);
}
function verifyLocation(lat, lng) {
    if (lat === undefined || lng === undefined || isNaN(lat) || isNaN(lng)) {
        return 'Please set a valid location'
    }
    lat = parseFloat(lat)
    lng = parseFloat(lng)
    if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
        return 'Invalid coordinates (lat: -90 to 90, lng: -180 to 180)'
    }
    if (lat === 0 && lng === 0) {
        return 'Please set your location on the map'
    }
    return null
}
</script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>