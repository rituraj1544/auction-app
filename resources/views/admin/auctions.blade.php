@extends('layouts.admin')

@section('title', 'Auctions')
@section('page-title', 'Auction Moderation')

@section('content')
    <div class="mb-5 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div class="flex flex-wrap gap-2">
            @foreach([null => 'All', 'active' => 'Active', 'upcoming' => 'Upcoming', 'closed' => 'Closed'] as $key => $label)
                <a href="{{ route('admin.auctions.index', array_filter(['status' => $key])) }}" class="rounded-full px-4 py-2 text-sm font-bold {{ $status === $key || ($status === null && $key === null) ? 'bg-slate-950 text-white' : 'bg-white text-slate-600' }}">{{ $label }}</a>
            @endforeach
        </div>
        <a href="{{ route('admin.auctions.create') }}" class="rounded-full bg-cyan-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-cyan-500/20">Create Auction</a>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead class="text-xs uppercase tracking-wider text-slate-500"><tr><th class="py-3">Auction</th><th>Seller</th><th>Category</th><th>Price</th><th>Winner / Bid Status</th><th>Ends</th><th>Status</th><th>Featured</th><th class="text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($auctions as $auction)
                        <tr>
                            <td class="py-4 font-black"><a href="{{ route('auctions.show', $auction) }}">{{ $auction->title }}</a></td>
                            <td>{{ $auction->user->name }}</td>
                            <td>{{ $auction->category->name }}</td>
                            <td>{{ $auction->isClosed() ? '$'.number_format($auction->finalBidAmount(), 2) : '$'.$auction->displayPrice() }}</td>
                            <td>
                                @if($auction->isClosed())
                                    <span class="font-bold">{{ $auction->winningUser()?->name ?? 'No bids' }}</span>
                                    <p class="text-xs text-slate-500">{{ $auction->bids_count }} total bids</p>
                                @else
                                    <span class="text-slate-500">{{ $auction->bids_count }} bids so far</span>
                                @endif
                            </td>
                            <td>{{ $auction->ends_at->format('M d, H:i') }}</td>
                            <td><span class="rounded-full px-3 py-1 text-xs font-bold {{ $auction->statusBadgeClasses() }}">{{ $auction->status() }}</span></td>
                            <td>{{ $auction->is_featured ? 'Yes' : 'No' }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.auctions.edit', $auction) }}" class="rounded-xl bg-slate-100 px-3 py-2 font-bold text-slate-700">Edit</a>
                                    <form method="POST" action="{{ route('admin.auctions.feature', $auction) }}">@csrf<button class="rounded-xl bg-cyan-50 px-3 py-2 font-bold text-cyan-700">{{ $auction->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
                                    @unless($auction->isClosed())
                                        <form method="POST" action="{{ route('admin.auctions.close', $auction) }}">@csrf<button class="rounded-xl bg-amber-50 px-3 py-2 font-bold text-amber-700">Close</button></form>
                                    @endunless
                                    <form method="POST" action="{{ route('admin.auctions.destroy', $auction) }}">@csrf @method('DELETE')<button class="rounded-xl bg-rose-50 px-3 py-2 font-bold text-rose-700" onclick="return confirm('Remove this auction?')">Remove</button></form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $auctions->links() }}</div>
    </div>
@endsection
