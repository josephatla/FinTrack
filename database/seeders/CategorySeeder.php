<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $defaultCategories = [
            'expense' => [
                'Food',
                'Transport',
                'Fashion',
                'Health',
                'Entertainment',
                'Gas',
                'Utilities',
                'Rent',
                'Subscription Fees',
                'Donations',
            ],

            'income' => [
                'Salary',
                'Bonus',
                'Freelance',
                'Investments',
                'Gifts Received',
                'Refunds',
                'Other Income',
            ],
        ];

        foreach ($defaultCategories as $type => $categories) {
            foreach ($categories as $categoryName) {
                DB::table('categories')->insert([
                    'user_id' => null, 
                    'name' => $categoryName,
                    'type' => $type, 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}