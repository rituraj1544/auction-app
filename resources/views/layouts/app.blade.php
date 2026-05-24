<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AuctionPro Marketplace')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute left-1/2 top-0 h-96 w-96 -translate-x-1/2 rounded-full bg-cyan-300/25 blur-3xl dark:bg-cyan-500/10"></div>
        <div class="absolute right-0 top-40 h-80 w-80 rounded-full bg-rose-300/20 blur-3xl dark:bg-rose-500/10"></div>
    </div>

    @include('partials.nav')

    <main>
        @include('partials.flash')
        @yield('content')
    </main>
</body>
</html>
