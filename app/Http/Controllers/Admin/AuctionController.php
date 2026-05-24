<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuctionRequest;
use App\Http\Requests\UpdateAuctionRequest;
use App\Models\Auction;
use App\Models\Category;
use App\Models\User;
use App\Notifications\AuctionWonNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuctionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = Auction::approved()->with(['user', 'category', 'winner', 'highestBid.user'])->withCount('bids');

        match ($status) {
            'active' => $query->active(),
            'upcoming' => $query->upcoming(),
            'closed' => $query->closed(),
            default => null,
        };

        return view('admin.auctions', [
            'auctions' => $query->latest()->paginate(15)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function create()
    {
        return view('admin.auction-form', [
            'auction' => new Auction([
                'condition' => 'Excellent',
                'min_increment' => 10,
                'starts_at' => now()->addHour(),
                'ends_at' => now()->addDays(7),
            ]),
            'categories' => Category::orderBy('name')->get(),
            'button' => 'Publish Auction',
        ]);
    }

    public function store(StoreAuctionRequest $request)
    {
        $attributes = $this->attributesFromRequest($request);
        $attributes['user_id'] = $request->user('admin')->id;
        $attributes['approval_status'] = 'approved';
        $attributes['current_price'] = null;

        if ($request->hasFile('image')) {
            $attributes['image_path'] = $request->file('image')->store('auctions', 'public');
            $attributes['gallery_images'] = [$attributes['image_path']];
        }

        Auction::create($attributes);

        return redirect()->route('admin.auctions.index')->with('success', 'Admin auction published.');
    }

    public function edit(Auction $auction)
    {
        return view('admin.auction-form', [
            'auction' => $auction,
            'categories' => Category::orderBy('name')->get(),
            'button' => 'Save Auction',
        ]);
    }

    public function update(UpdateAuctionRequest $request, Auction $auction)
    {
        $attributes = $this->attributesFromRequest($request);

        if ($request->hasFile('image')) {
            if ($auction->image_path) {
                Storage::disk('public')->delete($auction->image_path);
            }

            $attributes['image_path'] = $request->file('image')->store('auctions', 'public');
            $attributes['gallery_images'] = [$attributes['image_path']];
        }

        if (isset($attributes['ends_at']) && \Carbon\Carbon::parse($attributes['ends_at'])->isFuture()) {
            $attributes['closed_at'] = null;
            $attributes['winner_id'] = null;
        }

        $auction->update($attributes);

        return redirect()->route('admin.auctions.index')->with('success', 'Auction updated.');
    }

    public function feature(Auction $auction)
    {
        $auction->update(['is_featured' => ! $auction->is_featured]);

        return back()->with('success', 'Featured status updated.');
    }

    public function close(Auction $auction)
    {
        // Find the winner before closing
        $winnerId = $auction->highestBid?->user_id;

        $auction->forceFill([
            'ends_at'   => now(),
            'closed_at' => now(),
            'winner_id' => $winnerId,
        ])->save();

        // Send winner notification if there is a highest bidder
        if ($winnerId) {
            $winner = User::find($winnerId);
            $winner?->notify(new AuctionWonNotification($auction));
        }

        return back()->with('success', 'Auction closed. Winner notified.');
    }

    public function destroy(Auction $auction)
    {
        $auction->delete();

        return back()->with('success', 'Auction removed.');
    }

    private function attributesFromRequest(Request $request): array
    {
        $attributes = $request->validated();
        $attributes['condition'] = $attributes['condition'] ?? 'Excellent';
        $attributes['specifications'] = collect(preg_split('/\r\n|\r|\n/', $attributes['specifications'] ?? ''))
            ->filter()
            ->mapWithKeys(function (string $line) {
                [$key, $value] = array_pad(explode(':', $line, 2), 2, null);

                return $value ? [trim($key) => trim($value)] : [];
            })
            ->all();
        unset($attributes['image']);

        return $attributes;
    }
}
