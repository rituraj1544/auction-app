<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'category_id',
        'auction_id',
        'reviewed_by',
        'title',
        'description',
        'starting_price',
        'reserve_price',
        'min_increment',
        'image_path',
        'gallery_images',
        'condition',
        'specifications',
        'shipping_details',
        'starts_at',
        'ends_at',
        'status',
        'moderation_notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
            'reserve_price' => 'decimal:2',
            'min_increment' => 'decimal:2',
            'gallery_images' => 'array',
            'specifications' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'pending';
    }
}
