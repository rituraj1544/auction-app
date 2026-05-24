@csrf
<div class="grid gap-5">
    <div class="grid gap-5 md:grid-cols-[1.5fr_0.8fr]">
        <div>
            <label class="text-sm font-bold">Product title</label>
            <input name="title" value="{{ old('title', $auctionRequest->title) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900" placeholder="Apple iPhone 15 Pro Max - 256GB Titanium" required>
        </div>
        <div>
            <label class="text-sm font-bold">Category</label>
            <select name="category_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $auctionRequest->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <label class="text-sm font-bold">Realistic description</label>
        <textarea name="description" rows="6" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-cyan-500 dark:border-white/10 dark:bg-slate-900" required>{{ old('description', $auctionRequest->description) }}</textarea>
    </div>
    <div class="grid gap-5 md:grid-cols-5">
        <div><label class="text-sm font-bold">Starting price</label><input type="number" step="0.01" min="1" name="starting_price" value="{{ old('starting_price', $auctionRequest->starting_price) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required></div>
        <div><label class="text-sm font-bold">Reserve price</label><input type="number" step="0.01" min="1" name="reserve_price" value="{{ old('reserve_price', $auctionRequest->reserve_price) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900"></div>
        <div><label class="text-sm font-bold">Increment</label><input type="number" step="0.01" min="1" name="min_increment" value="{{ old('min_increment', $auctionRequest->min_increment ?? 10) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required></div>
        <div><label class="text-sm font-bold">Start</label><input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($auctionRequest->starts_at)->format('Y-m-d\\TH:i')) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required></div>
        <div><label class="text-sm font-bold">End</label><input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($auctionRequest->ends_at)->format('Y-m-d\\TH:i')) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required></div>
    </div>
    <div class="grid gap-5 md:grid-cols-2">
        <div><label class="text-sm font-bold">Condition</label><input name="condition" value="{{ old('condition', $auctionRequest->condition ?? 'Excellent') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" required></div>
        <div><label class="text-sm font-bold">Shipping details</label><input name="shipping_details" value="{{ old('shipping_details', $auctionRequest->shipping_details) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="Insured shipping within 2-4 business days"></div>
    </div>
    <div>
        <label class="text-sm font-bold">Specifications</label>
        <textarea name="specifications" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-900" placeholder="Storage: 256GB&#10;Condition: Like New">{{ old('specifications', collect($auctionRequest->specifications ?? [])->map(fn ($value, $key) => $key.': '.$value)->implode("\n")) }}</textarea>
    </div>
    <div x-data="{ preview: null }" class="rounded-3xl border border-dashed border-slate-300 p-5 dark:border-white/10">
        <label class="text-sm font-bold">Product image</label>
        <input type="file" name="image" class="mt-3 block w-full text-sm" accept="image/*" @change="preview = URL.createObjectURL($event.target.files[0])">
        <template x-if="preview"><img :src="preview" class="mt-4 h-48 w-full rounded-2xl object-cover"></template>
        @if($auctionRequest->image_path)
            <img src="{{ asset('storage/'.$auctionRequest->image_path) }}" class="mt-4 h-48 w-full rounded-2xl object-cover" alt="{{ $auctionRequest->title }}">
        @endif
    </div>
    <div class="flex flex-wrap gap-3">
        <button class="rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 px-7 py-3 font-black text-white disabled:cursor-not-allowed disabled:opacity-60" :disabled="submitting">
            <span x-show="!submitting">{{ $button }}</span>
            <span x-show="submitting">Submitting...</span>
        </button>
        <a href="{{ route('sell-requests.index') }}" class="rounded-full border border-slate-200 px-7 py-3 font-black dark:border-white/10">Cancel</a>
    </div>
</div>
