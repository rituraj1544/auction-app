<?php

use App\Models\Auction;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('auctions.{auction}', function ($user, Auction $auction) {
    return true;
});

Broadcast::channel('users.{userId}', function ($user, int $userId) {
    return (int) $user->id === $userId;
});
