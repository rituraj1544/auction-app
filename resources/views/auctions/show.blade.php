@extends('layouts.app')

@section('title', $auction->title)

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="space-y-5">
                <div class="overflow-hidden rounded-[2.5rem] border border-white/60 bg-white/80 p-4 shadow-2xl backdrop-blur dark:border-white/10 dark:bg-white/5">
                    <div class="aspect-[4/3] overflow-hidden rounded-[2rem] bg-slate-200 dark:bg-slate-800">
                        @if($auction->image_path)
                            <img src="{{ asset('storage/'.$auction->image_path) }}" class="h-full w-full object-cover" alt="{{ $auction->title }}">
                        @else
                            <div class="skeleton h-full w-full"></div>
                        @endif
                    </div>
                    <div class="mt-4 grid grid-cols-4 gap-3">
                        @foreach(($auction->gallery_images ?? [$auction->image_path]) as $galleryImage)
                            <img src="{{ asset('storage/'.$galleryImage) }}" class="aspect-square rounded-2xl object-cover" alt="{{ $auction->title }}">
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
                    <h2 class="text-xl font-black">Seller Information</h2>
                    <div class="mt-4 flex items-center gap-4">
                        <div class="grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 font-black text-white">{{ Str::substr($auction->user->name, 0, 1) }}</div>
                        <div>
                            <p class="font-bold">{{ $auction->user->name }}</p>
                            <p class="text-sm text-slate-500">Verified marketplace seller</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
                    <h2 class="text-xl font-black">Product specifications</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach(($auction->specifications ?? []) as $label => $value)
                            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-900"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $label }}</p><p class="mt-1 font-black">{{ $value }}</p></div>
                        @endforeach
                    </div>
                    <div class="mt-4 rounded-2xl bg-cyan-50 p-4 text-sm font-semibold text-cyan-800 dark:bg-cyan-950 dark:text-cyan-200">{{ $auction->shipping_details ?? 'Insured shipping available after payment confirmation.' }}</div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2.5rem] border border-white/60 bg-white/85 p-6 shadow-2xl backdrop-blur dark:border-white/10 dark:bg-white/5" data-auction-id="{{ $auction->id }}">
                    <div class="mb-4 flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-black text-cyan-700 dark:bg-cyan-950 dark:text-cyan-200">{{ $auction->category->name }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600 dark:bg-slate-900 dark:text-slate-300">{{ $auction->bids_count }} bids</span>
                        <span class="rounded-full px-3 py-1 text-xs font-black {{ $auction->statusBadgeClasses() }}">{{ $auction->status() }}</span>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700 dark:bg-emerald-950 dark:text-emerald-200">{{ $auction->condition }}</span>
                    </div>
                    <h1 class="text-4xl font-black tracking-tight">{{ $auction->title }}</h1>
                    <p class="mt-4 leading-7 text-slate-600 dark:text-slate-300">{{ $auction->description }}</p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-3xl bg-slate-50 p-5 dark:bg-slate-900">
                            <p class="text-sm text-slate-500">{{ $auction->isClosed() ? 'Final bid' : 'Highest bid' }}</p>
                            <p class="mt-2 whitespace-nowrap text-2xl font-black text-cyan-600 sm:text-3xl">
    $<span id="current-bid">{{ $auction->isClosed() ? number_format($auction->finalBidAmount(), 2) : $auction->displayPrice() }}</span>
</p>
                        </div>
                        @if($auction->isClosed())
                            <div class="rounded-3xl bg-slate-50 p-5 dark:bg-slate-900">
                                <p class="text-sm text-slate-500">Winner</p>
                                <p class="mt-2 text-3xl font-black">{{ $auction->winningUser()?->name ?? 'No winning bid' }}</p>
                            </div>
                        @else
                            <div class="rounded-3xl bg-slate-50 p-5 dark:bg-slate-900">
                                <p class="text-sm text-slate-500">Next minimum</p>
                                <p class="mt-2 text-3xl font-black">$<span id="minimum-next-bid">{{ number_format($auction->minimumNextBid(), 2) }}</span></p>
                            </div>
                        @endif
                        <div class="rounded-3xl bg-gradient-to-br from-slate-950 to-slate-800 p-5 text-white">
                            <p class="text-sm text-white/60">{{ $auction->isUpcoming() ? 'Starts in' : ($auction->isClosed() ? 'Auction ended' : 'Time left') }}</p>
                            <p class="mt-2 text-4xl font-black" data-countdown="{{ $auction->countdownTarget()->toIso8601String() }}">{{ $auction->isClosed() ? 'Ended' : 'Live' }}</p>
                        </div>
                    </div>

                    @if($auction->isClosed())
                        <div class="mt-6 rounded-3xl border border-rose-200 bg-rose-50 p-5 dark:border-rose-900 dark:bg-rose-950/30">
                            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                                <div>
                                    <p class="text-sm font-bold uppercase tracking-[0.25em] text-rose-600 dark:text-rose-300">Auction Ended</p>
                                    <h2 class="mt-1 text-2xl font-black">Final result confirmed</h2>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-sm text-rose-600 dark:text-rose-300">Winning bidder</p>
                                    <p class="text-xl font-black">{{ $auction->winningUser()?->name ?? 'No bids placed' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl bg-white/75 p-4 dark:bg-white/10"><p class="text-xs text-slate-500">Final amount</p><p class="font-black">${{ number_format($auction->finalBidAmount(), 2) }}</p></div>
                                <div class="rounded-2xl bg-white/75 p-4 dark:bg-white/10"><p class="text-xs text-slate-500">Total bids</p><p class="font-black">{{ $auction->bids_count }}</p></div>
                                <div class="rounded-2xl bg-white/75 p-4 dark:bg-white/10"><p class="text-xs text-slate-500">Ended on</p><p class="font-black">{{ ($auction->closed_at ?? $auction->ends_at)->format('M d, Y H:i') }}</p></div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-slate-950">
                        @auth('web')
                            @if($auction->isActive())
                                <form method="POST" action="{{ route('auctions.bids.store', $auction) }}" class="flex flex-col gap-3 sm:flex-row">
                                    @csrf
                                    <input type="number" step="0.01" min="{{ $auction->minimumNextBid() }}" name="amount" class="min-w-0 flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-lg font-bold outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900" placeholder="{{ number_format($auction->minimumNextBid(), 2) }}" required>
                                    <button class="rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-8 py-4 font-black text-white shadow-lg shadow-cyan-500/20">Place Bid</button>
                                </form>
                            @else
                                <div class="rounded-2xl bg-slate-100 p-4 font-semibold text-slate-600 dark:bg-slate-900 dark:text-slate-300">
                                    {{ $auction->isUpcoming() ? 'Bidding opens when the auction starts.' : 'Auction Ended. No more bids are allowed.' }}
                                </div>
                            @endif
                        @else
                            <a href="{{ route('user.login') }}" class="block rounded-2xl bg-slate-950 px-8 py-4 text-center font-black text-white dark:bg-white dark:text-slate-950">Login to Bid</a>
                        @endauth
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
                    <h2 class="text-xl font-black">Live bid activity</h2>
                    <div id="bid-history" class="mt-4 max-h-96 space-y-3 overflow-auto pr-1">
                        @forelse($auction->bids as $bid)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-slate-900">
                                <div class="flex items-center justify-between gap-3"><span class="font-semibold">{{ $bid->user->name }}</span><strong class="text-cyan-600">${{ number_format((float) $bid->amount, 2) }}</strong></div>
                                <p class="mt-1 text-xs text-slate-500">{{ $bid->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-slate-500 dark:border-white/10">No bids yet. Be the first.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @if($similarAuctions->isNotEmpty())
            <div class="mt-12">
                <h2 class="mb-5 text-2xl font-black">Similar auctions</h2>
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    @foreach($similarAuctions as $auction)
                        @include('partials.auction-card')
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- ===== WINNER CELEBRATION MODAL ===== --}}
    @auth('web')
        @if($auction->isClosed() && $auction->winner_id === auth('web')->id())
            <div id="win-modal"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 style="background: rgba(0,0,0,0.7); backdrop-filter: blur(6px);">

                {{-- Confetti canvas --}}
                <canvas id="confetti-canvas" class="pointer-events-none fixed inset-0 z-10" style="width:100%;height:100%"></canvas>

                <div class="relative z-20 w-full max-w-lg overflow-hidden rounded-[2.5rem] bg-white shadow-2xl dark:bg-slate-900"
                     style="animation: winSlideUp 0.5s cubic-bezier(0.34,1.56,0.64,1) both;">

                    {{-- Gold gradient header --}}
                    <div class="relative overflow-hidden bg-gradient-to-br from-amber-400 via-yellow-300 to-orange-400 px-8 py-10 text-center">
                        <div class="absolute inset-0 opacity-20"
                             style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 30px 30px;"></div>
                        <p class="relative text-7xl" style="animation: trophyBounce 0.8s 0.4s cubic-bezier(0.34,1.56,0.64,1) both; display:inline-block;">🏆</p>
                        <h2 class="relative mt-3 text-3xl font-black text-amber-900">You Won!</h2>
                        <p class="relative mt-1 text-sm font-semibold text-amber-800">Congratulations, you are the winning bidder</p>
                    </div>

                    {{-- Details --}}
                    <div class="px-8 py-6">
                        <p class="line-clamp-2 text-center text-lg font-black text-slate-800 dark:text-white">{{ $auction->title }}</p>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-amber-50 p-4 text-center dark:bg-amber-950/30">
                                <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Your winning bid</p>
                                <p class="mt-1 text-2xl font-black text-amber-700">${{ number_format($auction->finalBidAmount(), 2) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 text-center dark:bg-white/5">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total bids</p>
                                <p class="mt-1 text-2xl font-black">{{ $auction->bids_count }}</p>
                            </div>
                        </div>

                        <p class="mt-4 rounded-2xl bg-cyan-50 px-4 py-3 text-center text-sm font-semibold text-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-300">
                            🔔 A notification has been sent to your account
                        </p>

                        <div class="mt-5 flex gap-3">
                            <a href="{{ route('notifications.index') }}"
                               class="flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/5">
                                View Notifications
                            </a>
                            <button onclick="document.getElementById('win-modal').remove()"
                                    class="flex-1 rounded-2xl bg-gradient-to-r from-amber-400 to-orange-500 px-4 py-3 text-sm font-black text-white shadow-lg shadow-amber-500/30 transition hover:from-amber-500 hover:to-orange-600">
                                Awesome! 🎉
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                @keyframes winSlideUp {
                    from { opacity: 0; transform: translateY(60px) scale(0.9); }
                    to   { opacity: 1; transform: translateY(0)   scale(1);   }
                }
                @keyframes trophyBounce {
                    from { opacity: 0; transform: scale(0) rotate(-15deg); }
                    to   { opacity: 1; transform: scale(1) rotate(0deg);   }
                }
            </style>

            <script>
            // Simple confetti burst
            (function() {
                const canvas = document.getElementById('confetti-canvas');
                const ctx    = canvas.getContext('2d');
                canvas.width  = window.innerWidth;
                canvas.height = window.innerHeight;

                const colors  = ['#f59e0b','#f97316','#06b6d4','#8b5cf6','#ec4899','#10b981','#fbbf24'];
                const pieces  = Array.from({ length: 120 }, () => ({
                    x:   Math.random() * canvas.width,
                    y:   Math.random() * canvas.height - canvas.height,
                    r:   Math.random() * 7 + 3,
                    d:   Math.random() * 3 + 1.5,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    tilt: Math.random() * 10 - 5,
                    tiltAngle: 0,
                    tiltSpeed: Math.random() * 0.1 + 0.05,
                }));

                let frame = 0;
                function draw() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    pieces.forEach(p => {
                        p.tiltAngle += p.tiltSpeed;
                        p.y += p.d;
                        p.tilt = Math.sin(p.tiltAngle) * 12;
                        ctx.beginPath();
                        ctx.lineWidth = p.r;
                        ctx.strokeStyle = p.color;
                        ctx.moveTo(p.x + p.tilt + p.r / 2, p.y);
                        ctx.lineTo(p.x + p.tilt, p.y + p.tilt + p.r / 2);
                        ctx.stroke();
                        if (p.y > canvas.height) { p.y = -10; p.x = Math.random() * canvas.width; }
                    });
                    frame++;
                    if (frame < 220) requestAnimationFrame(draw);
                    else ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
                setTimeout(draw, 300);
            })();
            </script>
        @endif
    @endauth
@endsection
