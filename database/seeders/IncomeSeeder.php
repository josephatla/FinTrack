<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class IncomeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Fetch all user IDs
        $users = DB::table('users')->pluck('id')->toArray();
        
        // Fetch all categories once, then group them by type for efficiency
        $allCategories = DB::table('categories')->get()->groupBy('type');
        
        // Get array of income category IDs, or empty array if none exist
        $incomeCategories = $allCategories->get('income', collect())->pluck('category_id')->toArray();

        foreach ($users as $userId) {
            $accounts = DB::table('accounts')->where('user_id', $userId)->pluck('account_id')->toArray();
            
            for ($i = 0; $i < 10; $i++) {
                DB::table('incomes')->insert([
                    'user_id' => $userId,
                    'account_id' => $faker->randomElement($accounts),
                    'category_id' => $faker->randomElement($incomeCategories), 
                    'name' => 'Sample Income #' . ($i + 1),
                    'amount' => $faker->numberBetween(100000, 5000000),
                    'transaction_date' => $faker->dateTimeThisMonth(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}