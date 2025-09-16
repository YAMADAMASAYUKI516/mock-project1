<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = [
            'seller_id' => '1',
            'name' => '腕時計',
            'price' => '15000',
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image_path' => 'storage/items-image/Watch.jpg',
            'condition_id' => '1',
        ];
        DB::table('items')->insert($item);

        $item = [
            'seller_id' => '1',
            'name' => 'HDD',
            'price' => '5000',
            'brand' => '西芝',
            'description' => '高速で信頼性の高いハードディスク',
            'image_path' => 'storage/items-image/HDD.jpg',
            'condition_id' => '2',
        ];
        DB::table('items')->insert($item);

        $item = [
            'seller_id' => '1',
            'name' => '玉ねぎ3束',
            'price' => '300',
            'brand' => 'なし',
            'description' => '新鮮な玉ねぎ3束のセット',
            'image_path' => 'storage/items-image/Onion.jpg',
            'condition_id' => '3',
        ];
        DB::table('items')->insert($item);

        $item = [
            'seller_id' => '1',
            'name' => '革靴',
            'price' => '4000',
            'description' => 'クラシックなデザインの革靴',
            'image_path' => 'storage/items-image/Shoes.jpg',
            'condition_id' => '4',
        ];
        DB::table('items')->insert($item);

        $item = [
            'seller_id' => '1',
            'name' => 'ノートPC',
            'price' => '45000',
            'description' => '高性能なノートパソコン',
            'image_path' => 'storage/items-image/Laptop.jpg',
            'condition_id' => '1',
        ];
        DB::table('items')->insert($item);

        $item = [
            'seller_id' => '1',
            'name' => 'マイク',
            'price' => '8000',
            'brand' => 'なし',
            'description' => '高音質のレコーディング用マイク',
            'image_path' => 'storage/items-image/Mic.jpg',
            'condition_id' => '2',
        ];
        DB::table('items')->insert($item);

        $item = [
            'seller_id' => '1',
            'name' => 'ショルダーバッグ',
            'price' => '3500',
            'description' => 'おしゃれなショルダーバッグ',
            'image_path' => 'storage/items-image/Bag.jpg',
            'condition_id' => '3',
        ];
        DB::table('items')->insert($item);

        $item = [
            'seller_id' => '1',
            'name' => 'タンブラー',
            'price' => '500',
            'brand' => 'なし',
            'description' => '使いやすいタンブラー',
            'image_path' => 'storage/items-image/Tumbler.jpg',
            'condition_id' => '4',
        ];
        DB::table('items')->insert($item);

        $item = [
            'seller_id' => '1',
            'name' => 'コーヒーミル',
            'price' => '4000',
            'brand' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'image_path' => 'storage/items-image/CoffeeMill.jpg',
            'condition_id' => '1',
        ];
        DB::table('items')->insert($item);

        $item = [
            'seller_id' => '1',
            'name' => 'メイクセット',
            'price' => '2500',
            'description' => '便利なメイクアップセット',
            'image_path' => 'storage/items-image/MakeSet.jpg',
            'condition_id' => '2',
        ];
        DB::table('items')->insert($item);
    }
}
