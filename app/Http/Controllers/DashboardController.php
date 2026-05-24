<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $activeAuctions = Auction::approved()->with('category', 'highestBid.user')->withCount('bids')->active()->latest()->take(6)->get();
        $sellRequests = $user->auctionRequests()->with(['category', 'auction'])->latest()->paginate(6, ['*'], 'requests');
        $requestCounts = [
            'pending' => $user->auctionRequests()->where('status', 'pending')->count(),
            'approved' => $user->auctionRequests()->where('status', 'approved')->count(),
            'rejected' => $user->auctionRequests()->where('status', 'rejected')->count(),
        ];
        $approvedListings = $user->auctions()->approved()->with('category', 'highestBid.user')->withCount('bids')->latest()->paginate(5, ['*'], 'approved');
        $wonAuctions = $user->wonAuctions()->with('category', 'highestBid.user')->withCount('bids')->latest()->paginate(5, ['*'], 'won');
        $bids = $user->bids()
            ->whereHas('auction', fn ($query) => $query->approved()->active())
            ->with(['auction.category', 'auction.highestBid.user'])
            ->latest()
            ->paginate(8, ['*'], 'bids');

        return view('dashboard.index', compact('user', 'activeAuctions', 'sellRequests', 'requestCounts', 'approvedListings', 'wonAuctions', 'bids'));
    }
}
