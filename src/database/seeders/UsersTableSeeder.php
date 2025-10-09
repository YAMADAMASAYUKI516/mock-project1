<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'name' => 'seller',
            'email' => 'seller@a.com',
            'password' => Hash::make('seller01'),
            'email_verified_at' => Carbon::now(),
        ]);
    }
}