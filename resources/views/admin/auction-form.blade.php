@extends('layouts.admin')

@section('title', 'Auction Form')
@section('page-title', $auction->exists ? 'Edit Auction' : 'Create Auction')

@section('content')
    <form method="POST" action="{{ $auction->exists ? route('admin.auctions.update', $auction) : route('admin.auctions.store') }}" enctype="multipart/form-data" class="rounded-3xl bg-white p-6 shadow-sm">
        @csrf
        @if($auction->exists)
            @method('PUT')
        @endif
        @include('auctions._form', ['button' => $button, 'cancelRoute' => route('admin.auctions.index')])
    </form>
@endsection
