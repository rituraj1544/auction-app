<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - AuctionPro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    <div x-data="{ sidebar: false }" class="min-h-screen lg:flex">
        <aside class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full bg-slate-950 text-white transition lg:static lg:translate-x-0" :class="{ 'translate-x-0': sidebar }">
            <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-cyan-400 font-black text-slate-950">A</div>
                <div>
                    <div class="font-bold">AuctionPro</div>
                    <div class="text-xs text-slate-400">Admin Console</div>
                </div>
            </div>
            <nav class="space-y-1 p-4 text-sm">
                @php
                    $links = [
                        ['Dashboard', 'admin.dashboard', 'M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z'],
                        ['Seller Requests', 'admin.seller-requests.index', 'M12 6v12m6-6H6'],
                        ['Users', 'admin.users.index', 'M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zM8 11c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3z'],
                        ['Auctions', 'admin.auctions.index', 'M4 7h16M4 12h16M4 17h10'],
                        ['Categories', 'admin.categories.index', 'M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z'],
                        ['Bids', 'admin.bids.index', 'M12 8c-2.21 0-4 1.12-4 2.5S9.79 13 12 13s4-1.12 4-2.5S14.21 8 12 8z'],
                        ['Reports', 'admin.reports', 'M4 19V5m5 14V9m5 10V7m5 12v-6'],
                    ];
                @endphp
                @foreach($links as [$label, $route, $path])
                    <a href="{{ route($route) }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-white/10 {{ request()->routeIs($route) ? 'bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-400/20' : 'text-slate-300' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" /></svg>
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <button class="rounded-xl border border-slate-200 p-2 lg:hidden" @click="sidebar = !sidebar">Menu</button>
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Management</p>
                        <h1 class="font-bold">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Logout</button>
                    </form>
                </div>
            </header>
            <main class="p-4 sm:p-6 lg:p-8">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
