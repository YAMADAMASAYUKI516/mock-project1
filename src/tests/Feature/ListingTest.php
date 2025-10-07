<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function listing_all_items()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeTextInOrder(
            $items->pluck('name')->toArray()
        );
    }

    /** @test */
    public function listing_sold()
    {
        $item = Item::factory()->create();
        Order::factory()->create(['item_id' => $item->id]);

        $response = $this->get('/');

        $response->assertSee('sold');
    }

    /** @test */
    public function listing_my_item_hidden()
    {
        $user = User::factory()->create();

        $myItem = Item::factory()->create([
            'seller_id' => $user->id,
            'name' => 'My Unique Item',
        ]);

        $otherUser = User::factory()->create();
        $otherItem = Item::factory()->create([
            'seller_id' => $otherUser->id,
            'name' => 'Other Item',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertSee('Other Item');
        $response->assertDontSee('My Unique Item');
    }

    /** @test */
    public function mylist_like()
    {
        $user = User::factory()->create();
        $likedItem = Item::factory()->create();
        $unlikedItem = Item::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSeeText($likedItem->name);
        $response->assertDontSeeText($unlikedItem->name);
    }

    /** @test */
    public function mylist_sold()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        Order::factory()->create([
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('sold');
    }

    /** @test */
    public function mylist_uncertified()
    {
        $likedItem = Item::factory()->create();

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertDontSeeText($likedItem->name);
    }

    /** @test */
    public function search_partial_match()
    {
        Item::factory()->create(['name' => 'りんごジュース']);
        Item::factory()->create(['name' => 'オレンジジュース']);
        Item::factory()->create(['name' => 'バナナ']);

        $response = $this->get('/?keyword=ジュース');

        $response->assertSee('りんごジュース');
        $response->assertSee('オレンジジュース');
        $response->assertDontSee('バナナ');
    }

    /** @test */
    public function search_mylist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item1 = Item::factory()->create(['name' => 'チョコレート']);
        $item2 = Item::factory()->create(['name' => 'チョコバナナ']);
        $item3 = Item::factory()->create(['name' => 'ソフトクリーム']);

        $user->likes()->create(['item_id' => $item1->id]);
        $user->likes()->create(['item_id' => $item2->id]);
        $user->likes()->create(['item_id' => $item3->id]);

        $response = $this->get('/?tab=mylist&keyword=チョコ');

        $response->assertSee('チョコレート');
        $response->assertSee('チョコバナナ');
        $response->assertDontSee('ソフトクリーム');
    }
}
