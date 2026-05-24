<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuctionRequest;
use App\Http\Requests\UpdateAuctionRequest;
use App\Models\Auction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuctionController extends Controller
{
    public function index(Request $request)
    {
        $query = Auction::approved()
            ->with(['category', 'highestBid.user', 'winner', 'user'])
            ->withCount('bids')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%'.$request->search.'%');
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
            })
            ->when($request->filled('min_price'), fn ($query) => $query->whereRaw('COALESCE(current_price, starting_price) >= ?', [$request->min_price]))
            ->when($request->filled('max_price'), fn ($query) => $query->whereRaw('COALESCE(current_price, starting_price) <= ?', [$request->max_price]));

        match ($request->get('status', 'all')) {
            'upcoming' => $query->upcoming(),
            'closed' => $query->closed(),
            'active' => $query->active(),
            default => null,
        };

        $auctions = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('auctions.index', compact('auctions', 'categories'));
    }

    public function create()
    {
        return view('auctions.create', ['auction' => new Auction(), 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(StoreAuctionRequest $request)
    {
        $attributes = $request->validated();
        $attributes['user_id'] = $request->user()->id;
        $attributes['current_price'] = null;

        if ($request->hasFile('image')) {
            $attributes['image_path'] = $request->file('image')->store('auctions', 'public');
        }

        $auction = Auction::create($attributes);

        return redirect()->route('auctions.show', $auction)->with('success', 'Auction created successfully.');
    }

    public function show(Auction $auction)
    {
        abort_unless($auction->isApproved(), 404);

        $auction->loadCount('bids');
        $auction->load(['category', 'user', 'winner', 'highestBid.user', 'bids.user']);
        $auction->closeIfEnded();

        $similarAuctions = Auction::approved()
            ->with(['category', 'user'])
            ->withCount('bids')
            ->active()
            ->where('id', '!=', $auction->id)
            ->where('category_id', $auction->category_id)
            ->take(4)
            ->get();

        return view('auctions.show', [
            'auction' => $auction->fresh(['category', 'user', 'winner', 'highestBid.user', 'bids.user'])->loadCount('bids'),
            'similarAuctions' => $similarAuctions,
        ]);
    }

    public function edit(Auction $auction)
    {
        $this->authorize('update', $auction);

        return view('auctions.edit', ['auction' => $auction, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(UpdateAuctionRequest $request, Auction $auction)
    {
        $attributes = $request->validated();

        if ($request->hasFile('image')) {
            if ($auction->image_path) {
                Storage::disk('public')->delete($auction->image_path);
            }
            $attributes['image_path'] = $request->file('image')->store('auctions', 'public');
        }

        $auction->update($attributes);

        return redirect()->route('auctions.show', $auction)->with('success', 'Auction updated successfully.');
    }

    public function destroy(Auction $auction)
    {
        $this->authorize('delete', $auction);
        $auction->delete();

        return redirect()->route('dashboard')->with('success', 'Auction deleted.');
    }
}
