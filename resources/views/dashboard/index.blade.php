@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-300">Account overview</p>
                <h1 class="mt-2 text-4xl font-black">Welcome back, {{ $user->name }}</h1>
                <p class="mt-2 text-slate-500 dark:text-slate-400">Submit items for approval, track bids, and manage your marketplace activity.</p>
            </div>
            
        </div>

        <div class="grid gap-5 md:grid-cols-5">
            @foreach([
                ['Pending', $requestCounts['pending'], 'text-amber-600'],
                ['Approved', $requestCounts['approved'], 'text-emerald-600'],
                ['Rejected', $requestCounts['rejected'], 'text-rose-600'],
                ['Active bids', $bids->total(), 'text-cyan-600'],
                ['Won auctions', $user->wonAuctions()->count(), 'text-violet-600'],
            ] as [$label, $value, $color])
                <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
                    <p class="text-sm font-bold text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-4xl font-black {{ $color }}">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-8">
                <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-xl font-black">Sell requests</h2>
                        <a href="{{ route('sell-requests.index') }}" class="text-sm font-bold text-cyan-600">View all</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($sellRequests as $request)
                            <div class="rounded-3xl border border-slate-200 p-4 dark:border-white/10">
                                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                    <div>
                                        <p class="font-black">{{ $request->title }}</p>
                                        <p class="text-sm text-slate-500">{{ $request->category->name }} · Submitted {{ $request->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="w-fit rounded-full px-3 py-1 text-xs font-black {{ $request->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($request->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($request->status) }}</span>
                                </div>
                                @if($request->status === 'pending')
                                    <a href="{{ route('sell-requests.edit', $request) }}" class="mt-3 inline-flex text-sm font-bold text-cyan-600">Edit before approval</a>
                                @elseif($request->auction)
                                    <a href="{{ route('auctions.show', $request->auction) }}" class="mt-3 inline-flex text-sm font-bold text-cyan-600">View approved auction</a>
                                @elseif($request->moderation_notes)
                                    <p class="mt-3 rounded-2xl bg-rose-50 p-3 text-sm text-rose-700 dark:bg-rose-950 dark:text-rose-200">{{ $request->moderation_notes }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-white/10">No sell requests yet.</div>
                        @endforelse
                    </div>
                    <div class="mt-4">{{ $sellRequests->links() }}</div>
                </div>

                <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
                    <h2 class="mb-4 text-xl font-black">Approved listings</h2>
                    <div class="space-y-3">
                        @forelse($approvedListings as $auction)
                            <div class="flex flex-col justify-between gap-3 rounded-2xl bg-slate-50 p-4 dark:bg-slate-900 sm:flex-row sm:items-center">
                                <div>
                                    <a href="{{ route('auctions.show', $auction) }}" class="font-bold">{{ $auction->title }}</a>
                                    <p class="text-sm text-slate-500">{{ $auction->status() }} · {{ $auction->bids_count }} bids</p>
                                </div>
                                <span class="font-black">${{ $auction->displayPrice() }}</span>
                            </div>
                        @empty
                            <p class="text-slate-500">Approved auctions will appear here after admin review.</p>
                        @endforelse
                    </div>
                    <div class="mt-4">{{ $approvedListings->links() }}</div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
                    <div class="mb-4 flex items-center justify-between"><h2 class="text-xl font-black">My active bids</h2><a href="{{ route('bids.mine') }}" class="text-sm font-bold text-cyan-600">All</a></div>
                    <div class="space-y-3">
                        @forelse($bids->take(5) as $bid)
                            <div class="rounded-2xl border border-slate-200 p-4 dark:border-white/10">
                                <p class="font-bold">{{ $bid->auction->title }}</p>
                                <p class="text-sm text-slate-500">${{ number_format((float) $bid->amount, 2) }} · {{ $bid->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="text-slate-500">No bids yet.</p>
                        @endforelse
                    </div>
                </div>
                <div class="rounded-[2rem] bg-gradient-to-br from-cyan-500 to-blue-700 p-6 text-white shadow-xl shadow-cyan-500/20">
                    <h2 class="text-xl font-black">Won auctions</h2>
                    <p class="mt-2 text-white/80">Track your successful bids and payment-ready wins.</p>
                    <a href="{{ route('won-auctions') }}" class="mt-5 inline-flex rounded-full bg-white px-5 py-3 text-sm font-black text-blue-700">View wins</a>
                </div>
                <div class="rounded-[2rem] bg-white/80 p-6 shadow-xl dark:bg-white/5">
                    <h2 class="text-xl font-black">Profile</h2>
                    <div class="mt-4 space-y-3">
                        <div class="text-sm">
                            <p class="font-bold text-slate-600 dark:text-slate-400">Name</p>
                            <p class="text-slate-900 dark:text-white">{{ $user->name }}</p>
                        </div>
                        @if($user->email)
                        <div class="text-sm">
                            <p class="font-bold text-slate-600 dark:text-slate-400">Email</p>
                            <p class="text-slate-900 dark:text-white">{{ $user->email }}</p>
                        </div>
                        @endif
                        @if($user->phone)
                        <div class="text-sm">
                            <p class="font-bold text-slate-600 dark:text-slate-400">Phone</p>
                            <p class="text-slate-900 dark:text-white">{{ $user->phone }}</p>
                        </div>
                        @endif
                        @if($user->address || $user->city)
                        <div class="text-sm">
                            <p class="font-bold text-slate-600 dark:text-slate-400">Location</p>
                            <p class="text-slate-900 dark:text-white">{{ $user->address }}{{ $user->address && $user->city ? ', ' : '' }}{{ $user->city }}</p>
                        </div>
                        @endif
                    </div>
                    <a href="{{ route('profile.edit') }}" class="mt-5 inline-flex rounded-full bg-cyan-100 px-5 py-2 text-sm font-bold text-cyan-700 hover:bg-cyan-200 dark:bg-cyan-900/30 dark:text-cyan-300">Edit profile</a>
                </div>
            </div>
        </div>
    </section>
@endsection
