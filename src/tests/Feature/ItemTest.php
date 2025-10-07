<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function item_detail_all()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $condition = Condition::factory()->create(['condition' => '新品']);
        $category = Category::factory()->create(['name' => '家電']);

        $item = Item::factory()->create([
            'name' => '高性能カメラ',
            'brand' => 'CANON',
            'price' => 50000,
            'description' => 'ズームも撮影も完璧',
            'condition_id' => $condition->id,
            'image_path' => 'items-image/sample.jpg',
        ]);

        $item->categories()->attach($category->id);

        $item->likes()->create(['user_id' => User::factory()->create()->id]);
        $item->likes()->create(['user_id' => User::factory()->create()->id]);

        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'body' => 'とても良い商品です',
        ]);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('高性能カメラ');
        $response->assertSee('CANON');
        $response->assertSee('￥50,000');
        $response->assertSee('ズームも撮影も完璧');
        $response->assertSee('新品');
        $response->assertSee('家電');
        $response->assertSee('2');
        $response->assertSee('1');
        $response->assertSee('テストユーザー');
        $response->assertSee('とても良い商品です');
        $response->assertSee('items-image/sample.jpg');
    }

    /** @test */
    public function item_detail_many_category()
    {
        $category1 = Category::factory()->create(['name' => '家電']);
        $category2 = Category::factory()->create(['name' => 'カメラ']);

        $item = Item::factory()->create();
        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('家電');
        $response->assertSee('カメラ');
    }

    /** @test */
    public function like_success()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/like/{$item->id}");

        $response->assertOk();
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function like_check()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}");
        $this->assertStringContainsString('data-liked="false"', $response->getContent());

        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $this->assertStringContainsString('data-liked="false"', $response->getContent());

        $this->actingAs($user)->post("/like/{$item->id}");
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $this->assertStringContainsString('data-liked="true"', $response->getContent());
    }

    /** @test */
    public function like_unlike()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)->post("/like/{$item->id}");

        $response = $this->actingAs($user)->delete("/unlike/{$item->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function comment_login_success()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'body' => 'これはテストコメントです。',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'これはテストコメントです。',
        ]);
    }

    /** @test */
    public function comment_logout_failed()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comment", [
            'body' => '未ログインコメント',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'body' => '未ログインコメント',
        ]);
    }

    /** @test */
    public function comment_null()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->from("/item/{$item->id}")->post("/item/{$item->id}/comment", [
            'body' => '',
        ]);

        $response->assertRedirect("/item/{$item->id}");
        $response->assertSessionHasErrors(['body']);
    }

    /** @test */
    public function comment_max()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)->from("/item/{$item->id}")->post("/item/{$item->id}/comment", [
            'body' => $longComment,
        ]);

        $response->assertRedirect("/item/{$item->id}");
        $response->assertSessionHasErrors(['body']);
    }
}
