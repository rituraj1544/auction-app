<?php

namespace App\Notifications;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OutbidNotification extends Notification
{
    use Queueable;

    public function __construct(private Auction $auction, private Bid $bid)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been outbid')
            ->line('A higher bid was placed on '.$this->auction->title.'.')
            ->line('New highest bid: $'.number_format((float) $this->bid->amount, 2))
            ->action('View Auction', route('auctions.show', $this->auction));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auction_id' => $this->auction->id,
            'title'      => $this->auction->title,
            'message'    => 'You were outbid on '.$this->auction->title.'. New highest bid: $'.number_format((float) $this->bid->amount, 2).'.',
            'url'        => route('auctions.show', $this->auction),
        ];
    }
}
