<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionRequest;
use App\Models\Bid;
use App\Models\Category;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $monthlyBids = Bid::query()
            ->where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn (Bid $bid) => $bid->created_at->format('M'))
            ->map(fn ($bids) => $bids->sum('amount'));

        return view('admin.dashboard', [
            'usersCount' => User::count(),
            'auctionsCount' => Auction::approved()->count(),
            'activeCount' => Auction::approved()->active()->count(),
            'upcomingCount' => Auction::approved()->upcoming()->count(),
            'closedCount' => Auction::approved()->closed()->count(),
            'pendingRequestsCount' => AuctionRequest::pending()->count(),
            'approvedRequestsCount' => AuctionRequest::where('status', 'approved')->count(),
            'rejectedRequestsCount' => AuctionRequest::where('status', 'rejected')->count(),
            'bidsCount' => Bid::count(),
            'categoriesCount' => Category::count(),
            'revenue' => Bid::sum('amount'),
            'latestAuctions' => Auction::approved()->with(['user', 'category'])->latest()->take(8)->get(),
            'chartLabels' => $monthlyBids->keys(),
            'chartValues' => $monthlyBids->values(),
        ]);
    }
}
