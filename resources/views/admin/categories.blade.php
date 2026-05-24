@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Category Management')

@section('content')
    <div class="grid gap-6 lg:grid-cols-[360px_1fr]">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="h-fit rounded-3xl bg-white p-6 shadow-sm">
            @csrf
            <h2 class="text-xl font-black">Create category</h2>
            <input name="name" class="mt-5 w-full rounded-2xl border border-slate-200 px-4 py-3" placeholder="Category name" required>
            <button class="mt-4 w-full rounded-2xl bg-slate-950 px-5 py-3 font-black text-white">Create</button>
        </form>
        <div class="rounded-3xl bg-white p-6 shadow-sm">
            <div class="space-y-3">
                @foreach($categories as $category)
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-4">
                        <div><p class="font-black">{{ $category->name }}</p><p class="text-sm text-slate-500">{{ $category->auctions_count }} auctions</p></div>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">@csrf @method('DELETE')<button class="rounded-xl bg-rose-50 px-3 py-2 text-sm font-bold text-rose-700">Delete</button></form>
                    </div>
                @endforeach
            </div>
            <div class="mt-5">{{ $categories->links() }}</div>
        </div>
    </div>
@endsection
