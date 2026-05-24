<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionRequest;
use Illuminate\Http\Request;

class AuctionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $requests = AuctionRequest::with(['seller', 'category', 'auction'])
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.auction-requests', compact('requests', 'status'));
    }

    public function approve(Request $request, AuctionRequest $auctionRequest)
    {
        abort_unless($auctionRequest->status === 'pending', 422, 'Only pending requests can be approved.');

        $startsAt = $auctionRequest->starts_at->lt(now()) ? now() : $auctionRequest->starts_at;
        $endsAt = $auctionRequest->ends_at->lte($startsAt) ? $startsAt->copy()->addDays(7) : $auctionRequest->ends_at;

        $auction = Auction::create([
            'user_id' => $auctionRequest->seller_id,
            'category_id' => $auctionRequest->category_id,
            'title' => $auctionRequest->title,
            'description' => $auctionRequest->description,
            'starting_price' => $auctionRequest->starting_price,
            'min_increment' => $auctionRequest->min_increment,
            'current_price' => null,
            'image_path' => $auctionRequest->image_path,
            'gallery_images' => $auctionRequest->gallery_images,
            'condition' => $auctionRequest->condition,
            'specifications' => $auctionRequest->specifications,
            'shipping_details' => $auctionRequest->shipping_details,
            'approval_status' => 'approved',
            'auction_request_id' => $auctionRequest->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        $auctionRequest->update([
            'status' => 'approved',
            'auction_id' => $auction->id,
            'reviewed_by' => $request->user('admin')->id,
            'reviewed_at' => now(),
            'moderation_notes' => $request->input('moderation_notes'),
        ]);

        return back()->with('success', 'Sell request approved and published.');
    }

    public function reject(Request $request, AuctionRequest $auctionRequest)
    {
        abort_unless($auctionRequest->status === 'pending', 422, 'Only pending requests can be rejected.');

        $attributes = $request->validate([
            'moderation_notes' => ['required', 'string', 'max:2000'],
        ]);

        $auctionRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user('admin')->id,
            'reviewed_at' => now(),
            'moderation_notes' => $attributes['moderation_notes'],
        ]);

        return back()->with('success', 'Sell request rejected.');
    }
}
