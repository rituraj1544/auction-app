@extends('layouts.app')

@section('title', 'Won Auctions')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="mb-6 text-4xl font-black">Won auctions</h1>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse($auctions as $auction)
                @include('partials.auction-card')
            @empty
                <div class="col-span-full rounded-[2rem] border border-dashed border-slate-300 p-12 text-center dark:border-white/10">No won auctions yet.</div>
            @endforelse
        </div>
        <div class="mt-8">{{ $auctions->links() }}</div>
    </section>
@endsection
