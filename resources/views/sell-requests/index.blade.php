@extends('layouts.app')

@section('title', 'Sell Requests')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-cyan-600">Seller center</p>
                <h1 class="mt-2 text-4xl font-black">Sell requests</h1>
            </div>
            <a href="{{ route('sell-requests.create') }}" class="rounded-full bg-slate-950 px-6 py-3 text-sm font-black text-white dark:bg-white dark:text-slate-950">New Request</a>
        </div>
        <div class="space-y-4">
            @forelse($requests as $request)
                <div class="rounded-[2rem] bg-white/80 p-5 shadow-xl dark:bg-white/5">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div>
                            <p class="text-xl font-black">{{ $request->title }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $request->category->name }} · ${{ number_format((float) $request->starting_price, 2) }}</p>
                        </div>
                        <span class="w-fit rounded-full px-3 py-1 text-xs font-black {{ $request->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($request->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($request->status) }}</span>
                    </div>
                    @if($request->status === 'pending')
                        <a href="{{ route('sell-requests.edit', $request) }}" class="mt-4 inline-flex text-sm font-black text-cyan-600">Edit request</a>
                    @elseif($request->auction)
                        <a href="{{ route('auctions.show', $request->auction) }}" class="mt-4 inline-flex text-sm font-black text-cyan-600">View auction</a>
                    @elseif($request->moderation_notes)
                        <p class="mt-4 rounded-2xl bg-rose-50 p-4 text-sm font-semibold text-rose-700 dark:bg-rose-950 dark:text-rose-200">{{ $request->moderation_notes }}</p>
                    @endif
                </div>
            @empty
                <div class="rounded-[2rem] border border-dashed border-slate-300 p-12 text-center dark:border-white/10">No sell requests yet.</div>
            @endforelse
        </div>
        <div class="mt-8">{{ $requests->links() }}</div>
    </section>
@endsection
