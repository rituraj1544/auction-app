<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() || ($this->user()?->can('update', $this->route('auction')) ?? false);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'starting_price' => ['required', 'numeric', 'min:1'],
            'min_increment' => ['required', 'numeric', 'min:1'],
            'condition' => ['nullable', 'string', 'max:80'],
            'shipping_details' => ['nullable', 'string', 'max:255'],
            'specifications' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
