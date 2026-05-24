<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;

class AuctionPolicy
{
    public function update(User $user, Auction $auction): bool
    {
        return ($user->id === $auction->user_id || $user->isAdmin()) && $auction->canBeEdited();
    }

    public function delete(User $user, Auction $auction): bool
    {
        return $user->isAdmin() || ($user->id === $auction->user_id && $auction->canBeEdited());
    }
}
