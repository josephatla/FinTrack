<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $userIds = \DB::table('users')->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            for ($i = 0; $i < 2; $i++) {
                \DB::table('budgets')->insert([
                    'user_id' => $userId,
                    'name' => ucfirst($faker->word) . ' Limit',
                    'amount' => $faker->numberBetween(1000000, 10000000),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
