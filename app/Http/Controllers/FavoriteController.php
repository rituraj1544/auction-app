<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $auctions = $request->user()
            ->favoriteAuctions()
            ->with(['category', 'user'])
            ->withCount('bids')
            ->latest('auction_user_favorites.created_at')
            ->paginate(9);

        return view('dashboard.watchlist', compact('auctions'));
    }

    public function toggle(Request $request, Auction $auction)
    {
        $request->user()->favoriteAuctions()->toggle($auction->id);

        return back()->with('success', 'Watchlist updated.');
    }
}
