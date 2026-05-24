@extends('layouts.admin')

@section('title', 'Seller Requests')
@section('page-title', 'Seller Request Moderation')

@section('content')
    <div class="mb-5 flex flex-wrap gap-2">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
            <a href="{{ route('admin.seller-requests.index', ['status' => $key]) }}" class="rounded-full px-4 py-2 text-sm font-bold {{ $status === $key ? 'bg-slate-950 text-white' : 'bg-white text-slate-600' }}">{{ $label }}</a>
        @endforeach
    </div>
    <div class="space-y-4">
        @forelse($requests as $request)
            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <div class="grid gap-5 lg:grid-cols-[1fr_340px]">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-xl font-black">{{ $request->title }}</h2>
                            <span class="rounded-full px-3 py-1 text-xs font-black {{ $request->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($request->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($request->status) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-500">{{ $request->seller->name }} · {{ $request->category->name }} · Reserve ${{ number_format((float) $request->reserve_price, 2) }}</p>
                        <p class="mt-4 text-slate-600">{{ $request->description }}</p>
                        @if($request->moderation_notes)
                            <p class="mt-4 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">{{ $request->moderation_notes }}</p>
                        @endif
                    </div>
                    <div>
                        @if($request->image_path)
                            <img src="{{ asset('storage/'.$request->image_path) }}" class="h-44 w-full rounded-2xl object-cover" alt="{{ $request->title }}">
                        @endif
                        @if($request->status === 'pending')
                            <form method="POST" action="{{ route('admin.seller-requests.approve', $request) }}" class="mt-4 space-y-3">
                                @csrf
                                <textarea name="moderation_notes" rows="2" class="w-full rounded-2xl border border-slate-200 px-4 py-3" placeholder="Optional approval note"></textarea>
                                <button class="w-full rounded-2xl bg-emerald-600 px-4 py-3 font-black text-white">Approve & Publish</button>
                            </form>
                            <form method="POST" action="{{ route('admin.seller-requests.reject', $request) }}" class="mt-3 space-y-3">
                                @csrf
                                <textarea name="moderation_notes" rows="2" class="w-full rounded-2xl border border-slate-200 px-4 py-3" placeholder="Required rejection reason" required></textarea>
                                <button class="w-full rounded-2xl bg-rose-600 px-4 py-3 font-black text-white">Reject</button>
                            </form>
                        @elseif($request->auction)
                            <a href="{{ route('auctions.show', $request->auction) }}" class="mt-4 block rounded-2xl bg-slate-950 px-4 py-3 text-center font-black text-white">View Auction</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">No {{ $status }} requests.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $requests->links() }}</div>
@endsection
