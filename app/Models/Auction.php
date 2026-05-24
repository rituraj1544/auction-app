<?php

namespace App\Models;

use App\Notifications\AuctionWonNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'starting_price',
        'min_increment',
        'current_price',
        'winner_id',
        'image_path',
        'condition',
        'gallery_images',
        'specifications',
        'shipping_details',
        'approval_status',
        'is_featured',
        'auction_request_id',
        'starts_at',
        'ends_at',
        'closed_at',
        'ending_soon_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
            'min_increment' => 'decimal:2',
            'current_price' => 'decimal:2',
            'gallery_images' => 'array',
            'specifications' => 'array',
            'is_featured' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'closed_at' => 'datetime',
            'ending_soon_notified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class)->latest();
    }

    public function highestBid()
    {
        return $this->hasOne(Bid::class)->ofMany('amount', 'max');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function request()
    {
        return $this->belongsTo(AuctionRequest::class, 'auction_request_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('starts_at', '<=', now())->where('ends_at', '>', now())->whereNull('closed_at');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now())->whereNull('closed_at');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNotNull('closed_at')->orWhere('ends_at', '<=', now());
        });
    }

    public function isActive(): bool
    {
        return $this->isApproved() && $this->starts_at->lte(now()) && $this->ends_at->gt(now()) && is_null($this->closed_at);
    }

    public function isUpcoming(): bool
    {
        return $this->isApproved() && $this->starts_at->gt(now()) && is_null($this->closed_at);
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null || $this->ends_at->lte(now());
    }

    public function status(): string
    {
        if (! $this->isApproved()) {
            return ucfirst($this->approval_status);
        }

        if ($this->isClosed()) {
            return 'Closed';
        }

        return $this->isUpcoming() ? 'Upcoming' : 'Active';
    }

    public function statusBadgeClasses(): string
    {
        return match ($this->status()) {
            'Active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-200',
            'Upcoming' => 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-200',
            'Closed' => 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-200',
            default => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-200',
        };
    }

    public function countdownTarget()
    {
        return $this->isUpcoming() ? $this->starts_at : $this->ends_at;
    }

    public function canBeEdited(): bool
    {
        return $this->isUpcoming() && $this->bids()->doesntExist();
    }

    public function displayPrice(): string
    {
        return number_format((float) ($this->current_price ?? $this->starting_price), 2);
    }

    public function winningUser(): ?User
    {
        return $this->winner ?: $this->highestBid?->user;
    }

    public function finalBidAmount(): float
    {
        return (float) ($this->highestBid?->amount ?? $this->current_price ?? $this->starting_price);
    }

    public function minimumNextBid(): float
    {
        return (float) ($this->current_price ?? $this->starting_price) + (float) $this->min_increment;
    }

    public function closeIfEnded(): void
    {
        if (! $this->isApproved() || $this->closed_at || $this->ends_at->gt(now())) {
            return;
        }

        $winnerId = $this->highestBid?->user_id;

        $this->forceFill([
            'closed_at' => now(),
            'winner_id' => $winnerId,
        ])->save();

        // Load winner by ID directly — $this->winner is null after forceFill
        if ($winnerId) {
            $winner = \App\Models\User::find($winnerId);
            $winner?->notify(new AuctionWonNotification($this));
        }
    }
}
