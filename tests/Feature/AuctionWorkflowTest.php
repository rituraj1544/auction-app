<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_status_filters_render_active_upcoming_and_closed_auctions(): void
    {
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);

        Auction::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Active iPhone Auction',
            'description' => 'Approved live listing.',
            'starting_price' => 100,
            'min_increment' => 10,
            'approval_status' => 'approved',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
        ]);

        Auction::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Upcoming Watch Auction',
            'description' => 'Approved upcoming listing.',
            'starting_price' => 100,
            'min_increment' => 10,
            'approval_status' => 'approved',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
        ]);

        Auction::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Closed Camera Auction',
            'description' => 'Approved closed listing.',
            'starting_price' => 100,
            'min_increment' => 10,
            'approval_status' => 'approved',
            'starts_at' => now()->subDays(3),
            'ends_at' => now()->subDay(),
            'closed_at' => now()->subDay(),
        ]);

        $this->get('/auctions?status=active')->assertOk()->assertSee('Active iPhone Auction')->assertDontSee('Upcoming Watch Auction');
        $this->get('/auctions?status=upcoming')->assertOk()->assertSee('Upcoming Watch Auction')->assertDontSee('Closed Camera Auction');
        $this->get('/auctions?status=closed')->assertOk()->assertSee('Closed Camera Auction')->assertDontSee('Active iPhone Auction');
    }

    public function test_users_do_not_have_direct_auction_create_route(): void
    {
        $this->actingAs(User::factory()->create(), 'web')
            ->get('/auctions/create')
            ->assertNotFound();
    }
}
