<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuctionRequestSubmission extends StoreAuctionRequestSubmission
{
    public function authorize(): bool
    {
        return $this->user()?->id === $this->route('auction_request')?->seller_id
            && $this->route('auction_request')?->canBeEdited();
    }
}
