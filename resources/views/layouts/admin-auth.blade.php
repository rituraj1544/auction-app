<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Login') - AuctionPro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(34,211,238,.25),transparent_35%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,.2),transparent_35%)]"></div>
    <header class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-cyan-400 font-black text-slate-950">A</span><span class="font-black">AuctionPro Admin</span></a>
        <a href="{{ route('user.login') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-bold text-slate-300 hover:bg-white/10">User site</a>
    </header>
    @include('partials.flash')
    @yield('content')
</body>
</html>
