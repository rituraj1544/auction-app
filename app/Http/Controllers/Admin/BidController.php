<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bid;

class BidController extends Controller
{
    public function index()
    {
        return view('admin.bids', [
            'bids' => Bid::with(['user', 'auction.category'])->latest()->paginate(20),
        ]);
    }
}
