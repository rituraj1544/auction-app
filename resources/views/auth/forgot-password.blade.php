@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <section class="grid min-h-[calc(100vh-5rem)] place-items-center px-4 py-12">
        <form method="POST" action="{{ route('password.email') }}" class="w-full max-w-md rounded-[2rem] bg-white/85 p-8 shadow-2xl dark:bg-white/5">
            @csrf
            <p class="text-sm font-bold uppercase tracking-[0.25em] text-cyan-600">Account recovery</p>
            <h1 class="mt-2 text-3xl font-black">Reset password</h1>
            <p class="mt-3 text-slate-500">Enter your email and we will send a reset link.</p>
            <input name="email" type="email" value="{{ old('email') }}" class="mt-6 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required>
            <button class="mt-4 w-full rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-4 font-black text-white">Send Reset Link</button>
        </form>
    </section>
@endsection
