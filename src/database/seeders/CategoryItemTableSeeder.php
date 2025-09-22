<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Item;

class CategoryItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categoryItems = [
            '腕時計' => ['ファッション', 'メンズ'],
            'HDD' => ['家電'],
            '玉ねぎ3束' => ['キッチン'],
            '革靴' => ['ファッション'],
            'ノートPC' => ['家電'],
            'マイク' => ['家電'],
            'ショルダーバッグ' => ['ファッション', 'レディース'],
            'タンブラー' => ['キッチン'],
            'コーヒーミル' => ['キッチン'],
            'メイクセット' => ['レディース', 'コスメ'],
        ];

        foreach ($categoryItems as $itemName => $categoryNames) {
            $item = Item::where('name', $itemName)->first();

            if (!$item) {
                continue;
            }

            $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id');

            $item->categories()->syncWithoutDetaching($categoryIds);
        }
    }
}
