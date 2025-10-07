<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'postal_code' => $this->faker->postcode(),
            'address_line1' => $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress(),
            'address_line2' => $this->faker->secondaryAddress(),
            'avatar_path' => 'profile_images/sample.jpg',
        ];
    }
}
