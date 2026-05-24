@if(session('success') || session('error') || $errors->any())
    <div class="fixed right-4 top-24 z-50 w-[calc(100%-2rem)] max-w-md space-y-3">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4200)" x-transition class="rounded-2xl border border-emerald-200 bg-white/95 p-4 text-sm font-semibold text-emerald-700 shadow-2xl backdrop-blur dark:border-emerald-800 dark:bg-slate-900 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-white/95 p-4 text-sm font-semibold text-rose-700 shadow-2xl backdrop-blur dark:border-rose-800 dark:bg-slate-900 dark:text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-white/95 p-4 text-sm text-rose-700 shadow-2xl backdrop-blur dark:border-rose-800 dark:bg-slate-900 dark:text-rose-300">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
