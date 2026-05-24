@extends('layouts.app')

@section('title', 'My Bids')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400">Bidding history</p>
            <h1 class="mt-1 text-4xl font-black">My Bids</h1>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $bids->total() }} bid{{ $bids->total() !== 1 ? 's' : '' }} placed</p>
    </div>

    @php
        $leading  = $bids->filter(fn($b) => !$b->auction->isClosed() && $b->auction->highestBid?->user_id === auth('web')->id());
        $outbid   = $bids->filter(fn($b) => !$b->auction->isClosed() && $b->auction->highestBid?->user_id !== auth('web')->id());
        $won      = $bids->filter(fn($b) => $b->auction->isClosed() && $b->auction->winner_id === auth('web')->id());
        $lost     = $bids->filter(fn($b) => $b->auction->isClosed() && $b->auction->winner_id !== auth('web')->id());
    @endphp

    {{-- Stats bar --}}
    @if($bids->isNotEmpty())
    <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl bg-emerald-50 p-4 dark:bg-emerald-950/30">
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Leading</p>
            <p class="mt-1 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $leading->count() }}</p>
        </div>
        <div class="rounded-2xl bg-amber-50 p-4 dark:bg-amber-950/30">
            <p class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Outbid</p>
            <p class="mt-1 text-3xl font-black text-amber-700 dark:text-amber-300">{{ $outbid->count() }}</p>
        </div>
        <div class="rounded-2xl bg-violet-50 p-4 dark:bg-violet-950/30">
            <p class="text-xs font-bold uppercase tracking-wider text-violet-600 dark:text-violet-400">Won</p>
            <p class="mt-1 text-3xl font-black text-violet-700 dark:text-violet-300">{{ $won->count() }}</p>
        </div>
        <div class="rounded-2xl bg-slate-100 p-4 dark:bg-white/5">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Lost</p>
            <p class="mt-1 text-3xl font-black text-slate-600 dark:text-slate-300">{{ $lost->count() }}</p>
        </div>
    </div>
    @endif

    {{-- Bid cards --}}
    <div class="space-y-4">
        @forelse($bids as $bid)
            @php
                $auction       = $bid->auction;
                $highestBid    = $auction->highestBid;
                $isLeading     = !$auction->isClosed() && $highestBid?->user_id === auth('web')->id();
                $isOutbid      = !$auction->isClosed() && $highestBid?->user_id !== auth('web')->id() && $highestBid !== null;
                $isWon         = $auction->isClosed() && $auction->winner_id === auth('web')->id();
                $isLost        = $auction->isClosed() && $auction->winner_id !== auth('web')->id();
                $isClosed      = $auction->isClosed();

                $statusLabel = match(true) {
                    $isLeading => '🟢 Leading',
                    $isOutbid  => '🔴 Outbid',
                    $isWon     => '🏆 Won',
                    $isLost    => '❌ Lost',
                    default    => '⏳ Pending',
                };
                $cardBorder = match(true) {
                    $isLeading => 'border-emerald-200 dark:border-emerald-800',
                    $isOutbid  => 'border-amber-200 dark:border-amber-800',
                    $isWon     => 'border-violet-200 dark:border-violet-800',
                    $isLost    => 'border-slate-200 dark:border-white/10',
                    default    => 'border-slate-200 dark:border-white/10',
                };
                $badgeCls = match(true) {
                    $isLeading => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200',
                    $isOutbid  => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200',
                    $isWon     => 'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-200',
                    $isLost    => 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',
                    default    => 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',
                };
            @endphp

            <div class="overflow-hidden rounded-3xl border {{ $cardBorder }} bg-white/90 shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl dark:bg-white/5">
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center">

                    {{-- Thumbnail --}}
                    <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-slate-200 dark:bg-slate-800">
                        @if($auction->image_path)
                            <img src="{{ asset('storage/'.$auction->image_path) }}" class="h-full w-full object-cover" alt="{{ $auction->title }}">
                        @else
                            <div class="grid h-full place-items-center text-xs text-slate-400">No img</div>
                        @endif
                    </div>

                    {{-- Main info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('auctions.show', $auction) }}"
                               class="font-black text-slate-900 hover:text-cyan-600 dark:text-white dark:hover:text-cyan-400 transition line-clamp-1">
                                {{ $auction->title }}
                            </a>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $badgeCls }}">{{ $statusLabel }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            {{ $auction->category->name }}
                            &middot;
                            {{ $auction->bids_count }} total bid{{ $auction->bids_count !== 1 ? 's' : '' }}
                            &middot;
                            Your bid placed {{ $bid->created_at->diffForHumans() }}
                        </p>

                        {{-- Outbid warning --}}
                        @if($isOutbid)
                        <p class="mt-1.5 inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400">
                            ⚠ You've been outbid — current high: ${{ number_format((float) $highestBid->amount, 2) }}
                        </p>
                        @endif

                        {{-- Won message --}}
                        @if($isWon)
                        <p class="mt-1.5 inline-flex items-center gap-1 text-xs font-bold text-violet-600 dark:text-violet-400">
                            🎉 Congratulations! You won this auction.
                        </p>
                        @endif
                    </div>

                    {{-- Stats column --}}
                    <div class="flex shrink-0 flex-row gap-6 sm:flex-col sm:items-end sm:gap-1">
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Your bid</p>
                            <p class="text-xl font-black text-cyan-600">${{ number_format((float) $bid->amount, 2) }}</p>
                        </div>

                        @if(!$isClosed && $highestBid)
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Current high</p>
                            <p class="font-black {{ $isLeading ? 'text-emerald-600' : 'text-rose-500' }}">
                                ${{ number_format((float) $highestBid->amount, 2) }}
                            </p>
                        </div>
                        @endif

                        @if(!$isClosed)
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Ends</p>
                            <p class="text-sm font-bold" data-countdown="{{ $auction->ends_at->toIso8601String() }}">Live</p>
                        </div>
                        @endif
                    </div>

                    {{-- Action button --}}
                    <div class="shrink-0">
                        @if($isOutbid)
                            <a href="{{ route('auctions.show', $auction) }}"
                               class="flex items-center gap-1 rounded-2xl bg-amber-500 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-amber-500/20 transition hover:bg-amber-600">
                                Bid Again →
                            </a>
                        @elseif($isLeading)
                            <a href="{{ route('auctions.show', $auction) }}"
                               class="flex items-center gap-1 rounded-2xl bg-emerald-500 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-600">
                                Watch →
                            </a>
                        @elseif($isWon)
                            <a href="{{ route('auctions.show', $auction) }}"
                               class="flex items-center gap-1 rounded-2xl bg-violet-600 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-violet-500/20 transition hover:bg-violet-700">
                                View Win →
                            </a>
                        @else
                            <a href="{{ route('auctions.show', $auction) }}"
                               class="flex items-center gap-1 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/10">
                                View →
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Outbid progress bar (shows how far behind you are) --}}
                @if($isOutbid && $bid->amount > 0 && $highestBid->amount > 0)
                    @php
                        $pct = min(100, round(($bid->amount / $highestBid->amount) * 100));
                    @endphp
                    <div class="h-1 w-full bg-slate-100 dark:bg-white/5">
                        <div class="h-1 bg-gradient-to-r from-amber-400 to-rose-500 transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                @elseif($isLeading)
                    <div class="h-1 w-full bg-emerald-500/20">
                        <div class="h-1 w-full bg-gradient-to-r from-emerald-400 to-cyan-500"></div>
                    </div>
                @elseif($isWon)
                    <div class="h-1 w-full bg-gradient-to-r from-violet-400 to-fuchsia-500"></div>
                @endif
            </div>
        @empty
            <div class="rounded-[2rem] border border-dashed border-slate-300 p-16 text-center dark:border-white/10">
                <p class="text-5xl">🎯</p>
                <p class="mt-4 text-xl font-black">No bids yet</p>
                <p class="mt-2 text-slate-500">Start bidding on live auctions to see your activity here.</p>
                <a href="{{ route('auctions.index') }}" class="mt-6 inline-flex rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 px-6 py-3 font-black text-white shadow-lg transition hover:-translate-y-0.5">
                    Explore Auctions
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $bids->links() }}</div>
</section>
@endsection
