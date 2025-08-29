<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js'])
        @stack('after-styles')
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
        <style>
        /* Critical Font Loading - Inline for Performance */
        *, *::before, *::after {
            font-family: "Manrope", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif !important;
        }
        body, html {
            font-family: "Manrope", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif !important;
        }
        </style>
        <title>@yield('title')</title>
        <meta name="description" content="LMS is an innovative online learning platform that empowers students and professionals with high-quality, accessible courses.">

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/logos/logo-64.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/images/logos/logo-64.png') }}">

        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="LMS Online Learning Platform - Learn Anytime, Anywhere">
        <meta property="og:description" content="LMS is an innovative online learning platform that empowers students and professionals with high-quality, accessible courses.">
        <meta property="og:image" content="https://LMS-platform.netlify.app/assets/images/logos/logo-64-big.png">
        <meta property="og:url" content="https://LMS-platform.netlify.app">
        <meta property="og:type" content="website">
    </head>
    <body>
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
