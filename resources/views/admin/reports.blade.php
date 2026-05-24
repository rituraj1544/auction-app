@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
    <div class="grid gap-5 md:grid-cols-3">
        <div class="rounded-3xl bg-white p-6 shadow-sm"><p class="text-sm font-bold text-slate-500">Total bid value</p><p class="mt-2 text-3xl font-black">${{ number_format((float) $totalBidValue, 2) }}</p></div>
        <div class="rounded-3xl bg-white p-6 shadow-sm"><p class="text-sm font-bold text-slate-500">Closed auctions</p><p class="mt-2 text-3xl font-black">{{ $closedAuctions }}</p></div>
        <div class="rounded-3xl bg-white p-6 shadow-sm"><p class="text-sm font-bold text-slate-500">Active sellers</p><p class="mt-2 text-3xl font-black">{{ $activeSellers }}</p></div>
    </div>
    <div class="mt-6 rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-black">Top auctions by bids</h2>
        <div class="mt-5 space-y-3">
            @foreach($topAuctions as $auction)
                <div class="flex justify-between rounded-2xl bg-slate-50 p-4"><span class="font-bold">{{ $auction->title }}</span><span>{{ $auction->bids_count }} bids</span></div>
            @endforeach
        </div>
    </div>
@endsection
