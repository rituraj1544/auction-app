<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AuctionWonNotification extends Notification
{
    use Queueable;

    public function __construct(private Auction $auction)
    {
    }

    /**
     * Only store in database — no mail required.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id'  => $this->auction->id,
            'title'       => $this->auction->title,
            'message'     => 'Congratulations! You won the auction for '.$this->auction->title.'.',
            'winning_bid' => $this->auction->displayPrice(),
            'url'         => route('auctions.show', $this->auction),
        ];
    }
}
