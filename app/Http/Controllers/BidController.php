<?php

namespace App\Http\Controllers;

use App\Events\BidPlaced;
use App\Http\Requests\PlaceBidRequest;
use App\Models\Auction;
use App\Models\Bid;
use App\Notifications\OutbidNotification;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    public function store(PlaceBidRequest $request, Auction $auction)
    {
        $bid = DB::transaction(function () use ($request, $auction) {
            $auction = Auction::whereKey($auction->id)->lockForUpdate()->firstOrFail();
            $auction->closeIfEnded();

            abort_unless($auction->isApproved() && $auction->isActive(), 422, 'This auction is not open for bidding.');
            abort_if($auction->user_id === $request->user()->id, 422, 'You cannot bid on your own auction.');

            $minimum = $auction->minimumNextBid();
            abort_if((float) $request->amount < $minimum, 422, 'Your bid must be at least $'.number_format($minimum, 2).'.');

            $previousHighest = $auction->highestBid()->with('user')->first();

            $bid = Bid::create([
                'auction_id' => $auction->id,
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
            ]);

            $auction->forceFill(['current_price' => $bid->amount])->save();

            if ($previousHighest && $previousHighest->user_id !== $request->user()->id) {
                $previousHighest->user->notify(new OutbidNotification($auction, $bid));
            }

            return $bid->load('user');
        });

        $auction->refresh();
        try {
            broadcast(new BidPlaced($auction, $bid))->toOthers();
        } catch (\Throwable $exception) {
            report($exception);
        }

        return back()->with('success', 'Bid placed successfully.');
    }
}
