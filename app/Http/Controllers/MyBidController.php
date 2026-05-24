<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyBidController extends Controller
{
    public function __invoke(Request $request)
    {
        $bids = $request->user()
            ->bids()
            ->with([
                'auction.category',
                'auction.user',
                'auction.highestBid.user',
                'auction' => fn ($q) => $q->withCount('bids'),
            ])
            ->latest()
            ->paginate(12);

        return view('dashboard.bids', compact('bids'));
    }
}
