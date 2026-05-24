<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;

class ReportController extends Controller
{
    public function __invoke()
    {
        return view('admin.reports', [
            'totalBidValue' => Bid::sum('amount'),
            'closedAuctions' => Auction::approved()->closed()->count(),
            'activeSellers' => User::whereHas('auctions')->count(),
            'topAuctions' => Auction::approved()->withCount('bids')->orderByDesc('bids_count')->take(8)->get(),
        ]);
    }
}
