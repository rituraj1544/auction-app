<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AuctionEndingSoonNotification extends Notification
{
    use Queueable;

    public function __construct(private Auction $auction)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Auction ending soon')
            ->line($this->auction->title.' is ending soon.')
            ->action('Place a Bid', route('auctions.show', $this->auction));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'title'      => $this->auction->title,
            'message'    => 'Auction ending soon: '.$this->auction->title.'. Current price: $'.$this->auction->displayPrice().'.',
            'url'        => route('auctions.show', $this->auction),
        ];
    }
}
