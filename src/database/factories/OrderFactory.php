<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'buyer_id' => User::factory(),
            'shipping_postal_code' => $this->faker->postcode,
            'shipping_address1' => $this->faker->streetAddress,
            'shipping_address2' => $this->faker->secondaryAddress,
            'payment_method' => $this->faker->randomElement(['credit_card', 'convenience_store']),
        ];
    }
}
