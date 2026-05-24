@extends('layouts.admin')

@section('title', 'Bids')
@section('page-title', 'Bid Monitoring')

@section('content')
    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="text-xs uppercase tracking-wider text-slate-500"><tr><th class="py-3">Bidder</th><th>Auction</th><th>Category</th><th>Amount</th><th>Time</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($bids as $bid)
                        <tr><td class="py-4 font-black">{{ $bid->user->name }}</td><td>{{ $bid->auction->title }}</td><td>{{ $bid->auction->category->name }}</td><td class="font-black text-cyan-700">${{ number_format((float) $bid->amount, 2) }}</td><td>{{ $bid->created_at->diffForHumans() }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $bids->links() }}</div>
    </div>
@endsection
