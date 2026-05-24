@extends('layouts.app')

@section('title', 'AuctionPro - Live Auction Marketplace')

@section('content')
    <section class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1.08fr_0.92fr] lg:px-8 lg:py-24">
        <div>
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-cyan-200 bg-white/70 px-4 py-2 text-sm font-bold text-cyan-700 shadow-sm dark:border-cyan-500/30 dark:bg-white/10 dark:text-cyan-200">
                Live auctions, verified sellers, instant bidding
            </div>
            <h1 class="max-w-4xl text-5xl font-black leading-tight tracking-tight text-slate-950 dark:text-white sm:text-7xl">
                Bid smarter on rare finds and everyday deals.
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 dark:text-slate-300">
                Discover live auctions with real-time bid updates, watchlists, countdowns, and a polished marketplace experience built for confident buyers and sellers.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('auctions.index') }}" class="rounded-full bg-slate-950 px-7 py-4 font-black text-white shadow-xl shadow-slate-900/20 transition hover:-translate-y-1 dark:bg-white dark:text-slate-950">Explore Auctions</a>
                @guest('web')
                    <a href="{{ route('user.register') }}" class="rounded-full border border-slate-300 bg-white/70 px-7 py-4 font-black text-slate-900 shadow-lg transition hover:-translate-y-1 dark:border-white/10 dark:bg-white/10 dark:text-white">Start Selling</a>
                @else
                    <a href="{{ route('sell-requests.create') }}" class="rounded-full border border-slate-300 bg-white/70 px-7 py-4 font-black text-slate-900 shadow-lg transition hover:-translate-y-1 dark:border-white/10 dark:bg-white/10 dark:text-white">Submit to Sell</a>
                @endguest
            </div>
        </div>
        <div class="relative">
            <div class="absolute -left-8 top-10 h-24 w-24 rounded-3xl bg-amber-300/70 blur-2xl"></div>
            <div class="animate-float rounded-[2.5rem] border border-white/60 bg-white/75 p-4 shadow-2xl shadow-cyan-900/10 backdrop-blur dark:border-white/10 dark:bg-white/10">
                @if($featuredAuctions->first())
                    @php($heroAuction = $featuredAuctions->first())
                    <div class="overflow-hidden rounded-[2rem] bg-slate-100 dark:bg-slate-900">
                        @if($heroAuction->image_path)
                            <img src="{{ asset('storage/'.$heroAuction->image_path) }}" class="h-72 w-full object-cover" alt="{{ $heroAuction->title }}">
                        @else
                            <div class="skeleton h-72 w-full"></div>
                        @endif
                    </div>
                    <div class="p-5">
                        <p class="text-sm font-bold text-cyan-600 dark:text-cyan-300">Featured live auction</p>
                        <h2 class="mt-2 text-2xl font-black">{{ $heroAuction->title }}</h2>
                    <div class="mt-5 grid grid-cols-3 gap-3">
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-900"><p class="text-xs text-slate-500">Current</p><p class="font-black">${{ $heroAuction->displayPrice() }}</p></div>
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-900"><p class="text-xs text-slate-500">Bids</p><p class="font-black">{{ $heroAuction->bids_count }}</p></div>
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-900"><p class="text-xs text-slate-500">Ends</p><p class="font-black" data-countdown="{{ $heroAuction->ends_at->toIso8601String() }}">Live</p></div>
                    </div>
                    </div>
                @else
                    <div class="skeleton h-96 rounded-[2rem]"></div>
                @endif
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-[2rem] bg-white/75 p-6 shadow-xl dark:bg-white/5"><p class="text-sm font-bold text-slate-500">Live auctions</p><p class="mt-2 text-4xl font-black">{{ number_format($stats['live']) }}</p></div>
            <div class="rounded-[2rem] bg-white/75 p-6 shadow-xl dark:bg-white/5"><p class="text-sm font-bold text-slate-500">Verified bids</p><p class="mt-2 text-4xl font-black">{{ number_format($stats['bids']) }}</p></div>
            <div class="rounded-[2rem] bg-white/75 p-6 shadow-xl dark:bg-white/5"><p class="text-sm font-bold text-slate-500">Active sellers</p><p class="mt-2 text-4xl font-black">{{ number_format($stats['sellers']) }}</p></div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-300">Live now</p>
                <h2 class="mt-2 text-3xl font-black">Featured Auctions</h2>
            </div>
            <a href="{{ route('auctions.index') }}" class="rounded-full border border-slate-200 px-5 py-2 text-sm font-bold hover:bg-white dark:border-white/10 dark:hover:bg-white/10">View all</a>
        </div>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @forelse($featuredAuctions as $auction)
                @include('partials.auction-card')
            @empty
                <div class="col-span-full rounded-[2rem] border border-dashed border-slate-300 bg-white/70 p-10 text-center dark:border-white/10 dark:bg-white/5">No active auctions yet.</div>
            @endforelse
        </div>
    </section>

    <section class="border-y border-white/60 bg-white/55 py-16 backdrop-blur dark:border-white/10 dark:bg-white/5">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div class="lg:col-span-1">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-rose-500">Trending</p>
                <h2 class="mt-2 text-3xl font-black">Most active bidding rooms</h2>
                <p class="mt-4 text-slate-600 dark:text-slate-300">These auctions are pulling the most live attention right now.</p>
            </div>
            <div class="grid gap-4 lg:col-span-2">
                @foreach($trendingAuctions as $auction)
                    <a href="{{ route('auctions.show', $auction) }}" class="flex items-center justify-between gap-4 rounded-3xl bg-white p-4 shadow-lg transition hover:-translate-y-1 dark:bg-slate-900">
                        <div>
                            <p class="font-black">{{ $auction->title }}</p>
                            <p class="text-sm text-slate-500">{{ $auction->category->name }} · {{ $auction->bids_count }} bids</p>
                        </div>
                        <div class="text-right"><p class="text-xs text-slate-500">Current</p><p class="font-black text-cyan-600">${{ $auction->displayPrice() }}</p></div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
            <h2 class="text-2xl font-black">Ending soon</h2>
            <div class="mt-5 space-y-3">
                @foreach($endingSoonAuctions as $auction)
                    <a href="{{ route('auctions.show', $auction) }}" class="flex justify-between rounded-2xl bg-slate-50 p-4 transition hover:-translate-y-1 dark:bg-slate-900">
                        <span class="font-bold">{{ $auction->title }}</span>
                        <span class="font-black text-rose-500" data-countdown="{{ $auction->ends_at->toIso8601String() }}">Live</span>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
            <h2 class="text-2xl font-black">Recently sold</h2>
            <div class="mt-5 space-y-3">
                @forelse($recentlySoldAuctions as $auction)
                    <div class="flex justify-between rounded-2xl bg-slate-50 p-4 dark:bg-slate-900">
                        <span class="font-bold">{{ $auction->title }}</span>
                        <span class="font-black text-emerald-600">${{ $auction->displayPrice() }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">Closed winning auctions will appear here.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] bg-slate-950 p-6 text-white shadow-2xl">
            <h2 class="text-2xl font-black">Top sellers this week</h2>
            <div class="mt-5 grid gap-3 md:grid-cols-5">
                @foreach($topSellers as $seller)
                    <div class="rounded-2xl bg-white/10 p-4">
                        <div class="grid h-12 w-12 place-items-center rounded-xl bg-cyan-400 font-black text-slate-950">{{ Str::substr($seller->name, 0, 1) }}</div>
                        <p class="mt-3 font-bold">{{ $seller->name }}</p>
                        <p class="text-sm text-white/60">{{ $seller->auctions_count }} listings</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
