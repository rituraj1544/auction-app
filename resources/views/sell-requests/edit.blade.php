@extends('layouts.app')

@section('title', 'Edit Sell Request')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6">
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-cyan-600">Seller request</p>
            <h1 class="mt-2 text-4xl font-black">Edit pending request</h1>
        </div>
        <form method="POST" action="{{ route('sell-requests.update', $auctionRequest) }}" enctype="multipart/form-data" class="rounded-[2rem] bg-white/80 p-6 shadow-2xl dark:bg-white/5" x-data="{ submitting: false }" @submit="submitting = true">
            @method('PUT')
            @include('sell-requests._form', ['button' => 'Save Request'])
        </form>
    </section>
@endsection
