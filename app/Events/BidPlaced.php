<?php

namespace App\Events;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Auction $auction, public Bid $bid)
    {
        $this->auction->loadMissing('category');
        $this->bid->loadMissing('user');
    }

    public function broadcastOn(): Channel
    {
        return new Channel('auctions.'.$this->auction->id);
    }

    public function broadcastAs(): string
    {
        return 'bid.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'auction_id' => $this->auction->id,
            'amount' => number_format((float) $this->bid->amount, 2, '.', ''),
            'bidder' => $this->bid->user->name,
            'placed_at' => $this->bid->created_at->diffForHumans(),
            'minimum_next_bid' => number_format($this->auction->minimumNextBid(), 2, '.', ''),
        ];
    }
}
