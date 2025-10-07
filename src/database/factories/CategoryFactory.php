<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    protected static $categoryNames = [
        'ファッション',
        '家電',
        'インテリア',
        'レディース',
        'メンズ',
        'コスメ',
        '本',
        'ゲーム',
        'スポーツ',
        'キッチン',
        'ハンドメイド',
        'アクセサリー',
        'おもちゃ',
        'ベビー・キッズ',
    ];

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(self::$categoryNames),
        ];
    }
}
