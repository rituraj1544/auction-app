@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['Users', $usersCount, 'from-blue-500 to-cyan-400'],
            ['Auctions', $auctionsCount, 'from-violet-500 to-fuchsia-400'],
            ['Live', $activeCount, 'from-emerald-500 to-teal-400'],
            ['Bids', $bidsCount, 'from-amber-500 to-orange-400'],
            ['Revenue', '$'.number_format((float) $revenue, 0), 'from-slate-900 to-slate-700'],
        ] as [$label, $value, $gradient])
            <div class="rounded-3xl bg-white p-5 shadow-sm">
                <div class="mb-5 h-2 w-16 rounded-full bg-gradient-to-r {{ $gradient }}"></div>
                <p class="text-sm font-bold text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-black">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-5 md:grid-cols-3">
        <a href="{{ route('admin.seller-requests.index', ['status' => 'pending']) }}" class="rounded-3xl bg-amber-50 p-5 shadow-sm transition hover:-translate-y-1">
            <p class="text-sm font-bold text-amber-700">Pending seller requests</p>
            <p class="mt-2 text-3xl font-black">{{ $pendingRequestsCount }}</p>
        </a>
        <a href="{{ route('admin.auctions.index', ['status' => 'upcoming']) }}" class="rounded-3xl bg-blue-50 p-5 shadow-sm transition hover:-translate-y-1">
            <p class="text-sm font-bold text-blue-700">Upcoming auctions</p>
            <p class="mt-2 text-3xl font-black">{{ $upcomingCount }}</p>
        </a>
        <a href="{{ route('admin.auctions.index', ['status' => 'closed']) }}" class="rounded-3xl bg-rose-50 p-5 shadow-sm transition hover:-translate-y-1">
            <p class="text-sm font-bold text-rose-700">Closed auctions</p>
            <p class="mt-2 text-3xl font-black">{{ $closedCount }}</p>
        </a>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-3xl bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between"><h2 class="text-xl font-black">Bid value trend</h2><span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700">6 months</span></div>
            <canvas id="revenueChart" height="120"></canvas>
        </div>
        <div class="rounded-3xl bg-slate-950 p-6 text-white shadow-sm">
            <h2 class="text-xl font-black">Operational snapshot</h2>
            <div class="mt-6 space-y-4">
                <div class="flex justify-between rounded-2xl bg-white/10 p-4"><span>Categories</span><strong>{{ $categoriesCount }}</strong></div>
                <div class="flex justify-between rounded-2xl bg-white/10 p-4"><span>Approved requests</span><strong>{{ $approvedRequestsCount }}</strong></div>
                <div class="flex justify-between rounded-2xl bg-white/10 p-4"><span>Rejected requests</span><strong>{{ $rejectedRequestsCount }}</strong></div>
                <div class="flex justify-between rounded-2xl bg-white/10 p-4"><span>Average bid</span><strong>${{ number_format((float) \App\Models\Bid::avg('amount'), 2) }}</strong></div>
            </div>
        </div>
    </div>

    <div class="mt-8 rounded-3xl bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center justify-between"><h2 class="text-xl font-black">Latest auctions</h2><a href="{{ route('admin.auctions.index') }}" class="text-sm font-bold text-cyan-600">Moderate all</a></div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="text-xs uppercase tracking-wider text-slate-500"><tr><th class="py-3">Title</th><th>Seller</th><th>Category</th><th>Price</th><th>Status</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($latestAuctions as $auction)
                        <tr><td class="py-4 font-bold">{{ $auction->title }}</td><td>{{ $auction->user->name }}</td><td>{{ $auction->category->name }}</td><td>${{ $auction->displayPrice() }}</td><td><span class="rounded-full px-3 py-1 text-xs font-bold {{ $auction->statusBadgeClasses() }}">{{ $auction->status() }}</span></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{ label: 'Bid value', data: @json($chartValues), borderColor: '#0891b2', backgroundColor: 'rgba(8,145,178,.12)', fill: true, tension: .4 }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });
        });
    </script>
@endsection
