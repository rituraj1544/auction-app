@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6"><p class="text-sm font-bold uppercase tracking-[0.25em] text-cyan-600">Account</p><h1 class="mt-2 text-4xl font-black">Profile settings</h1></div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="rounded-[2rem] bg-white/85 p-6 shadow-2xl dark:bg-white/5">
            @csrf @method('PATCH')
            <div class="grid gap-6">
                <div class="flex items-center gap-5">
                    <div class="grid h-20 w-20 place-items-center overflow-hidden rounded-3xl bg-gradient-to-br from-cyan-400 to-blue-600 text-2xl font-black text-white">
                        @if($user->avatar_path)
                            <img src="{{ asset('storage/'.$user->avatar_path) }}" class="h-full w-full object-cover" alt="{{ $user->name }}">
                        @else
                            {{ Str::substr($user->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <label class="text-sm font-bold">Avatar</label>
                        <input name="avatar" type="file" accept="image/*" class="mt-2 block text-sm">
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-bold">Name</label>
                        <input name="name" value="{{ old('name', $user->name) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required>
                    </div>
                    <div>
                        <label class="text-sm font-bold">Email</label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-bold">New password</label>
                        <input name="password" type="password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="Optional">
                    </div>
                    <div>
                        <label class="text-sm font-bold">Confirm password</label>
                        <input name="password_confirmation" type="password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="Optional">
                    </div>
                </div>
                <hr class="border-slate-200 dark:border-white/10">
                <div>
                    <h2 class="text-lg font-black">Contact & Location</h2>
                    <p class="mt-1 text-sm text-slate-500">Optional details to help buyers reach you</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-bold">Phone</label>
                        <input name="phone" value="{{ old('phone', $user->phone) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="Your phone number">
                    </div>
                    <div>
                        <label class="text-sm font-bold">Address</label>
                        <input name="address" value="{{ old('address', $user->address) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="Street address">
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-bold">City</label>
                        <input name="city" value="{{ old('city', $user->city) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="City">
                    </div>
                    <div>
                        <label class="text-sm font-bold">Country</label>
                        <input name="country" value="{{ old('country', $user->country) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="Country">
                    </div>
                </div>
                <button class="w-fit rounded-full bg-slate-950 px-7 py-3 font-black text-white dark:bg-white dark:text-slate-950">Save Profile</button>
            </div>
        </form>
    </section>
@endsection
