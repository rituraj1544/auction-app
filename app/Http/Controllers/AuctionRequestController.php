<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuctionRequestSubmission;
use App\Http\Requests\UpdateAuctionRequestSubmission;
use App\Models\AuctionRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuctionRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = $request->user()
            ->auctionRequests()
            ->with(['category', 'auction'])
            ->latest()
            ->paginate(10);

        return view('sell-requests.index', compact('requests'));
    }

    public function create()
    {
        return view('sell-requests.create', [
            'auctionRequest' => new AuctionRequest([
                'condition' => 'Excellent',
                'min_increment' => 10,
            ]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(StoreAuctionRequestSubmission $request)
    {
        $attributes = $this->attributesFromRequest($request);

        $existingRequest = AuctionRequest::where('seller_id', $request->user()->id)
            ->where('status', 'pending')
            ->where('title', $attributes['title'])
            ->first();

        if ($existingRequest) {
            return redirect()
                ->route('sell-requests.index')
                ->with('success', 'This item is already pending admin approval.');
        }

        $attributes['seller_id'] = $request->user()->id;
        $attributes['status'] = 'pending';

        if ($request->hasFile('image')) {
            $attributes['image_path'] = $request->file('image')->store('auction-requests', 'public');
            $attributes['gallery_images'] = [$attributes['image_path']];
        }

        AuctionRequest::create($attributes);

        return redirect()->route('sell-requests.index')->with('success', 'Sell request submitted for admin approval.');
    }

    public function edit(AuctionRequest $auctionRequest)
    {
        abort_unless($auctionRequest->seller_id === request()->user()->id && $auctionRequest->canBeEdited(), 403);

        return view('sell-requests.edit', [
            'auctionRequest' => $auctionRequest,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAuctionRequestSubmission $request, AuctionRequest $auctionRequest)
    {
        $attributes = $this->attributesFromRequest($request);

        if ($request->hasFile('image')) {
            if ($auctionRequest->image_path) {
                Storage::disk('public')->delete($auctionRequest->image_path);
            }

            $attributes['image_path'] = $request->file('image')->store('auction-requests', 'public');
            $attributes['gallery_images'] = [$attributes['image_path']];
        }

        $auctionRequest->update($attributes);

        return redirect()->route('sell-requests.index')->with('success', 'Sell request updated.');
    }

    private function attributesFromRequest(Request $request): array
    {
        $attributes = $request->validated();
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
