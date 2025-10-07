<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function exhibition_store()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $image = UploadedFile::fake()->image('item.jpg');

        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明文です。',
            'price' => 9999,
            'condition_id' => $condition->id,
            'category_ids' => [$category1->id, $category2->id],
            'image_path' => $image,
        ]);

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明文です。',
            'price' => 9999,
            'condition_id' => $condition->id,
            'seller_id' => $user->id,
        ]);
    }
}
