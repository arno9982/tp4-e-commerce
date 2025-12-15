<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- CSS -->
        <link rel="stylesheet" href="{{ asset('css/normalize-perso.css') }}">
        <link rel="stylesheet" href="{{ asset('css/base.css') }}">
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
        <link rel="stylesheet" href="{{ asset('css/product.css') }}">
        <link rel="stylesheet" href="{{ asset('styles/responsive.css') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">

        {{-- HEADER --}}
        @include('partials.header')

        <main>
            @yield('content')
        </main>

     {{-- FOOTER --}}
        @include('partials.footer')

        <!-- JS -->
        <script src="{{ asset('javascript/product.js') }}" defer></script>
        <script src="{{ asset('javascript/responsive.js') }}" defer></script>
    </body>
</html>
