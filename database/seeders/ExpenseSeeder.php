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

        // FIX 1: Fetch user IDs and their account_type
        $users = DB::table('users')->select('id', 'account_type')->get();
        
        // Fetch all categories once, then group them by type for efficiency
        $allCategories = DB::table('categories')->get()->groupBy('type');
        
        // Get array of expense category IDs (user-defined or system-wide), or empty array
        $expenseCategories = $allCategories->get('expense', collect())->pluck('category_id')->toArray();

        // Safety check: Ensure we have expense categories defined
        if (empty($expenseCategories)) {
            return; // Exit silently if no categories exist
        }

        foreach ($users as $user) {
            $userId = $user->id;
            
            // FIX 2: Check user status using account_type
            $isPremium = $user->account_type === 'PREMIUM'; 

            $accounts = DB::table('accounts')->where('user_id', $userId)->pluck('account_id')->toArray();

            // Safety check: Ensure the user has accounts before seeding
            if (empty($accounts)) {
                continue;
            }
            
            // Fetch budgets only if the user is premium
            $budgets = [];
            if ($isPremium && DB::getSchemaBuilder()->hasTable('budgets')) {
                $budgets = DB::table('budgets')->where('user_id', $userId)->pluck('budget_id')->toArray();
            }

            for ($i = 0; $i < 20; $i++) {
                
                // FIX 3: Conditionally determine budget_id BEFORE the insert call
                $budgetId = null;
                
                // Assign a budget only if: 
                // 1. The user is Premium 
                // 2. The user has created budgets 
                // 3. Random chance (50% probability)
                if ($isPremium && !empty($budgets) && $faker->boolean(50)) {
                    $budgetId = $faker->randomElement($budgets);
                }
                
                DB::table('expenses')->insert([
                    'user_id' => $userId,
                    'account_id' => $faker->randomElement($accounts),
                    
                    'category_id' => $faker->randomElement($expenseCategories), 
                    
                    // Use the conditionally determined $budgetId
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