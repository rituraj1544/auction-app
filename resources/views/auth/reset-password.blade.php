@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
    <section class="grid min-h-[calc(100vh-5rem)] place-items-center px-4 py-12">
        <form method="POST" action="{{ route('password.store') }}" class="w-full max-w-md rounded-[2rem] bg-white/85 p-8 shadow-2xl dark:bg-white/5">
            @csrf
            <h1 class="text-3xl font-black">Choose new password</h1>
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="mt-6 grid gap-4">
                <input name="email" type="email" value="{{ old('email', $request->email) }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required>
                <input name="password" type="password" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="Password" required>
                <input name="password_confirmation" type="password" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="Confirm password" required>
                <button class="rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-4 font-black text-white">Reset Password</button>
            </div>
        </form>
    </section>
@endsection
