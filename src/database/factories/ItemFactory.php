<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'seller_id' => User::factory(),
            'condition_id' => Condition::factory(),
            'name' => $this->faker->word,
            'price' => $this->faker->numberBetween(500, 10000),
            'description' => $this->faker->paragraph,
            'image_path' => 'storage/items-img/sample.jpg',
            'brand' => $this->faker->company,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Item $item) {
            $categories = Category::factory()->count(2)->create();
            $item->categories()->attach($categories->pluck('id'));
        });
    }
}
