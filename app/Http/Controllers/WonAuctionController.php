<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WonAuctionController extends Controller
{
    public function __invoke(Request $request)
    {
        $auctions = $request->user()
            ->wonAuctions()
            ->with(['category', 'user', 'highestBid.user', 'winner'])
            ->withCount('bids')
            ->latest()
            ->paginate(9);

        return view('dashboard.won', compact('auctions'));
    }
}
