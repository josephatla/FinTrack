<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create('id_ID');

        \DB::table('users')->insert([
            'name' => 'Premium Test User',
            'email' => 'premium@gmail.com',
            'password' => Hash::make('password'), 
            'account_type' => 'PREMIUM',
            'premium_expires_at' => now()->addYears(1), 
            'created_at' => now(), 
            'updated_at' => now(), 
        ]);

        \DB::table('users')->insert([
            'name' => 'Free Test User',
            'email' => 'free@gmail.com',
            'password' => Hash::make('password'),
            'account_type' => 'FREE',
            'premium_expires_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 0; $i < 8; $i++) {
            $isPremium = $faker->boolean(30); 
            
            $accountType = $isPremium ? 'PREMIUM' : 'FREE';
            $premiumExpires = $isPremium ? now()->addMonths($faker->numberBetween(1, 12))->toDateTimeString() : null;

            \DB::table('users')->insert([
                'name' => $faker->firstName . ' ' . $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'account_type' => $accountType,
                'premium_expires_at' => $premiumExpires,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}