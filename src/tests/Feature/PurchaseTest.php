<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function purchase_buy()
    {
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create();

        $this->actingAs($user)->withSession([
            "shipping_postal_code_{$item->id}" => '123-4567',
            "shipping_address1_{$item->id}" => '東京都新宿区',
            "shipping_address2_{$item->id}" => '西新宿2-8-1',
        ])->post("/purchase/{$item->id}", [
            'payment_method' => 'カード支払い',
        ]);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id' => $item->id,
            'shipping_postal_code' => '123-4567',
            'shipping_address1' => '東京都新宿区',
            'shipping_address2' => '西新宿2-8-1',
            'payment_method' => 'カード支払い',
        ]);
    }

    /** @test */
    public function purchase_sold()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        \App\Models\Order::factory()->create([
            'buyer_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertSee('sold');
    }

    /** @test */
    public function purchase_mypage()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        \App\Models\Order::factory()->create([
            'buyer_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/mypage?tab=purchased');

        $response->assertSee($item->name);
    }

    public function payment_method_reflection()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['_old_input' => ['payment_method' => 'カード支払い']])
            ->get("/purchase/{$item->id}");

        $response->assertStatus(200);

        $response->assertSee('支払い方法');
        $response->assertSee('コンビニ払い');
        $response->assertSee('カード支払い');

        $response->assertSee('カード支払い');
        $response->assertSee('js-payment-method');
    }

    /** @test */
    public function address_edit_reflection()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)->withSession([
            "shipping_postal_code_{$item->id}" => '123-4567',
            "shipping_address1_{$item->id}" => '東京都渋谷区',
            "shipping_address2_{$item->id}" => '桜丘町26-1'
        ]);

        $response = $this->get("/purchase/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('〒 123-4567');
        $response->assertSee('東京都渋谷区 桜丘町26-1');
    }

    /** @test */
    public function address_edit_store()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)->withSession([
            "shipping_postal_code_{$item->id}" => '987-6543',
            "shipping_address1_{$item->id}" => '大阪府大阪市',
            "shipping_address2_{$item->id}" => '北区梅田1-1'
        ])->post("/purchase/{$item->id}", [
            'payment_method' => 'カード支払い',
        ]);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id' => $item->id,
            'shipping_postal_code' => '987-6543',
            'shipping_address1' => '大阪府大阪市',
            'shipping_address2' => '北区梅田1-1',
        ]);
    }
}
