<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Notifications\AuctionEndingSoonNotification;
use Illuminate\Console\Command;

class CloseEndedAuctions extends Command
{
    protected $signature = 'auctions:close-ended';

    protected $description = 'Close ended auctions and send ending-soon reminders.';

    public function handle(): int
    {
        Auction::approved()
            ->whereNull('closed_at')
            ->where('ends_at', '<=', now())
            ->with('highestBid.user')
            ->get()
            ->each->closeIfEnded();

        Auction::approved()
            ->active()
            ->whereNull('ending_soon_notified_at')
            ->whereBetween('ends_at', [now(), now()->addHour()])
            ->with(['bids.user'])
            ->get()
            ->each(function (Auction $auction) {
                $auction->bids->pluck('user')->unique('id')->each->notify(new AuctionEndingSoonNotification($auction));
                $auction->forceFill(['ending_soon_notified_at' => now()])->save();
            });

        $this->info('Auction lifecycle checks completed.');

        return self::SUCCESS;
    }
}
