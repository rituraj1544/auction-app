@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black">Notifications</h1>
                @php $unreadCount = auth('web')->user()->unreadNotifications()->count(); @endphp
                @if($unreadCount > 0)
                    <p class="mt-1 text-sm font-semibold text-rose-500">{{ $unreadCount }} unread</p>
                @endif
            </div>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read') }}">
                    @csrf
                    <button class="rounded-full bg-slate-950 px-5 py-2 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $icon = match(true) {
                        str_contains($notification->type, 'Won')       => '🏆',
                        str_contains($notification->type, 'Outbid')    => '📣',
                        str_contains($notification->type, 'Ending')    => '⏳',
                        default                                         => '🔔',
                    };
                @endphp
                <div class="flex items-start gap-4 rounded-3xl border p-5 shadow-lg transition
                    {{ $isUnread
                        ? 'border-cyan-200 bg-cyan-50 dark:border-cyan-800 dark:bg-cyan-950/40'
                        : 'border-slate-200 bg-white/70 dark:border-white/10 dark:bg-white/5' }}">

                    {{-- Icon --}}
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-xl
                        {{ $isUnread ? 'bg-cyan-100 dark:bg-cyan-900' : 'bg-slate-100 dark:bg-white/10' }}">
                        {{ $icon }}
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-bold {{ $isUnread ? 'text-slate-950 dark:text-white' : 'text-slate-600 dark:text-slate-300' }}">
                            {{ $notification->data['message'] ?? 'Auction update' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>

                        <div class="mt-3 flex flex-wrap gap-3">
                            @if(!empty($notification->data['url']))
                                <a href="{{ route('notifications.mark-read', $notification->id) }}"
                                   class="inline-flex items-center gap-1 rounded-full bg-cyan-600 px-4 py-1.5 text-xs font-bold text-white transition hover:bg-cyan-700">
                                    View Auction →
                                </a>
                            @endif
                            @if($isUnread)
                                <a href="{{ route('notifications.mark-read', $notification->id) }}"
                                   class="inline-flex items-center gap-1 rounded-full border border-slate-300 px-4 py-1.5 text-xs font-bold text-slate-600 transition hover:border-slate-400 dark:border-white/20 dark:text-slate-300">
                                    Mark as read
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Unread dot --}}
                    @if($isUnread)
                        <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-cyan-500"></div>
                    @endif
                </div>
            @empty
                <div class="rounded-[2rem] border border-dashed border-slate-300 p-16 text-center dark:border-white/10">
                    <p class="text-5xl">🔔</p>
                    <p class="mt-4 text-lg font-bold text-slate-500">No notifications yet.</p>
                    <p class="mt-1 text-sm text-slate-400">You'll be notified when you win an auction or get outbid.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $notifications->links() }}</div>
    </section>
@endsection
