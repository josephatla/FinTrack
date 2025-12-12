<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $users = \DB::table('users')->pluck('id')->toArray();

        $defaultAccounts = [
            ['name' => 'Cash', 'type' => 'cash'],
            ['name' => 'BCA', 'type' => 'bank'],
            ['name' => 'BRI', 'type' => 'bank'],
            ['name' => 'OVO', 'type' => 'ewallet'],
            ['name' => 'GoPay', 'type' => 'ewallet'],
        ];

        foreach ($users as $userId) {
            foreach ($defaultAccounts as $acc) {
                \DB::table('accounts')->insert([
                    'user_id' => $userId,
                    'name' => $acc['name'],
                    'type' => $acc['type'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
