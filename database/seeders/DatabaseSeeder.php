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
        // Cetak indikator pelacak
        $this->command->info("--- SEEDER SEDANG BERJALAN SEKARANG ---");

        // 1. Jalankan Seeder Negara (Mengisi 195 negara)
        $this->call([
            CountrySeeder::class,
        ]);
        
        // 2. Buat Akun Administrator Default dengan proteksi updateOrInsert dan Kolom Role
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@supplysync.com'], // Jika email ini sudah ada, timpa saja datanya
            [
                'name' => 'Administrator',
                'role' => 'admin', // Menentukan hak akses sebagai admin
                'password' => Hash::make('12345678'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Isi Kamus Sentimen Positif dengan proteksi updateOrInsert
        $positiveWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'success', 'boom', 'surge'];
        foreach ($positiveWords as $word) {
            DB::table('positive_words')->updateOrInsert(
                ['word' => $word],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 4. Isi Kamus Sentimen Negatif dengan proteksi updateOrInsert
        $negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'decrease', 'drop', 'crash', 'loss'];
        foreach ($negativeWords as $word) {
            DB::table('negative_words')->updateOrInsert(
                ['word' => $word],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}