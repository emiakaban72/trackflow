<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@trackflow',
            'password' => Hash::make('12345678'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $countries = [
            ['name' => 'Germany', 'code' => 'DE', 'currency_code' => 'EUR'],
            ['name' => 'China', 'code' => 'CN', 'currency_code' => 'CNY'],
            ['name' => 'Indonesia', 'code' => 'ID', 'currency_code' => 'IDR'],
            ['name' => 'Australia', 'code' => 'AU', 'currency_code' => 'AUD'],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'name' => $country['name'],
                'code' => $country['code'],
                'currency_code' => $country['currency_code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $positiveWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'success', 'boom', 'surge'];
        foreach ($positiveWords as $word) {
            DB::table('positive_words')->insert([
                'word' => $word,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'decrease', 'drop', 'crash', 'loss'];
        foreach ($negativeWords as $word) {
            DB::table('negative_words')->insert([
                'word' => $word,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}