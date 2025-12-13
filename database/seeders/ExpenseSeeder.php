<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = DB::table('users')->select('id', 'account_type')->get();
        $allCategories = DB::table('categories')->get()->groupBy('type');
        $expenseCategories = $allCategories->get('expense', collect())->pluck('category_id')->toArray();

        foreach ($users as $user) {
            $userId = $user->id;
            $isPremium = $user->account_type === 'PREMIUM'; 
            $accounts = DB::table('accounts')->where('user_id', $userId)->pluck('account_id')->toArray();
            
            $budgets = [];
            if ($isPremium) {
                $budgets = DB::table('budgets')->where('user_id', $userId)->pluck('budget_id')->toArray();
            }

            for ($i = 0; $i < 20; $i++) {
                $budgetId = null;
                if ($isPremium && !empty($budgets)) {
                    $budgetId = $faker->randomElement($budgets);
                }
                
                DB::table('expenses')->insert([
                    'user_id' => $userId,
                    'account_id' => $faker->randomElement($accounts),
                    'category_id' => $faker->randomElement($expenseCategories), 
                    'budget_id' => $budgetId, 
                    'name' => 'Sample Expense #' . ($i + 1),
                    'amount' => $faker->numberBetween(5000, 1000000),
                    'transaction_date' => $faker->dateTimeThisMonth(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}