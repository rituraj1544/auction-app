<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Category;
use App\Models\User;

class HomeController extends Controller
{
    public function __invoke()
    {
        $featuredAuctions = Auction::approved()
            ->with(['category', 'highestBid.user', 'winner', 'user'])
            ->active()
            ->withCount('bids')
            ->latest()
            ->take(8)
            ->get();

        $trendingAuctions = Auction::approved()
            ->with(['category', 'user'])
            ->active()
            ->withCount('bids')
            ->orderByDesc('bids_count')
            ->take(6)
            ->get();

        $categories = Category::withCount('auctions')->orderBy('name')->take(6)->get();

        $endingSoonAuctions = Auction::approved()->with(['category', 'user'])->withCount('bids')->active()->orderBy('ends_at')->take(4)->get();
        $recentlySoldAuctions = Auction::approved()->with(['category', 'winner'])->withCount('bids')->closed()->whereNotNull('winner_id')->latest('closed_at')->take(4)->get();
        $topSellers = User::withCount('auctions')->whereHas('auctions')->orderByDesc('auctions_count')->take(5)->get();
        $stats = [
            'live' => Auction::approved()->active()->count(),
            'bids' => \App\Models\Bid::count(),
            'sellers' => User::whereHas('auctions')->count(),
        ];

        return view('home', compact('featuredAuctions', 'trendingAuctions', 'categories', 'endingSoonAuctions', 'recentlySoldAuctions', 'topSellers', 'stats'));
    }
}
