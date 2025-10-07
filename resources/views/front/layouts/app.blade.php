<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js'])
        @stack('after-styles')
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
        {{-- Global font diset via resources/css/app.css & custom.css --}}
        <title>@yield('title')</title>
        <meta name="description" content="LMS is an innovative online learning platform that empowers students and professionals with high-quality, accessible courses.">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/logos/favicon.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/images/logos/favicon.svg') }}">

        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="LMS Online Learning Platform - Learn Anytime, Anywhere">
        <meta property="og:description" content="LMS is an innovative online learning platform that empowers students and professionals with high-quality, accessible courses.">
        <meta property="og:image" content="/assets/images/logos/logo-64-big.png">
        <meta property="og:url" content="https://aksellera.com">
        <meta property="og:type" content="website">
    </head>
    <body class="font-manrope">
        <div class="min-h-screen flex flex-col">
            <div class="flex-1">
                @yield('content')
            </div>
            <x-simple-footer />
        </div>

        @stack('scripts')
        @stack('after-scripts')
    </body>
</html>
