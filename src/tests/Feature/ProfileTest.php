<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_get()
    {
        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        $itemSelling = Item::factory()->create([
            'seller_id' => $user->id,
            'name' => '出品商品A',
        ]);

        $itemPurchased = Item::factory()->create([
            'name' => '購入商品B',
        ]);

        Order::factory()->create([
            'buyer_id' => $user->id,
            'item_id' => $itemPurchased->id,
        ]);

        $responseSelling = $this->actingAs($user)->get('/mypage?tab=selling');

        $responseSelling->assertStatus(200);
        $responseSelling->assertSee('テスト太郎');
        $responseSelling->assertSee('出品商品A');
        $responseSelling->assertSee('<div class="profile__avatar-image">', false);

        $responsePurchased = $this->actingAs($user)->get('/mypage?tab=purchased');

        $responsePurchased->assertStatus(200);
        $responsePurchased->assertSee('購入商品B');
    }

    /** @test */
    public function profile_edit_old()
    {
        $user = User::factory()->create([
            'name' => 'テスト太郎',
        ]);

        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address_line1' => '東京都新宿区1-1-1',
            'address_line2' => 'テストビル101',
            'avatar_path' => 'profile_images/sample.jpg',
        ]);

        $response = $this->actingAs($user)->get('/edit');

        $response->assertStatus(200);

        $response->assertSee('value="テスト太郎"', false);

        $response->assertSee('value="123-4567"', false);

        $response->assertSee('value="東京都新宿区1-1-1"', false);
        $response->assertSee('value="テストビル101"', false);

        $response->assertSee('/storage/' . $profile->image_path, false);
    }
}
