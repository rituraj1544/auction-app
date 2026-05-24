<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuctionFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 day', '+2 days');
        $end = fake()->dateTimeBetween($start, '+8 days');
        $starting = fake()->numberBetween(25, 800);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->words(4, true),
            'description' => fake()->paragraphs(3, true),
            'starting_price' => $starting,
            'min_increment' => fake()->randomElement([5, 10, 25, 50]),
            'current_price' => null,
            'starts_at' => $start,
            'ends_at' => $end,
        ];
    }
}
