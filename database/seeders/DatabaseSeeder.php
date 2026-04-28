<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
// Pastikan baris WithoutModelEvents tetap ada jika dibutuhkan
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        User::firstOrCreate(
            ['email' => 'admin@perpus.com'],
            [
                'name' => 'Admin Perpustakaan',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'approved',
            ]
        );

        // 2. Create Petugas
        User::firstOrCreate(
            ['email' => 'petugas@perpus.com'],
            [
                'name' => 'Petugas Perpustakaan',
                'password' => bcrypt('password'),
                'role' => 'petugas',
                'status' => 'approved',
            ]
        );

        // 3. Create Siswa
        User::firstOrCreate(
            ['email' => 'siswa@perpus.com'],
            [
                'name' => 'Siswa Test',
                'password' => bcrypt('password'),
                'role' => 'anggota',
                'status' => 'approved',
                'nisn' => '001',
                'kelas' => '10-A',
                'phone' => '08123456789',
            ]
        );

        // 4. Panggil Seeder Lainnya
        // Pastikan file BookSeeder.php dan BorrowSeeder.php SUDAH ADA di folder seeders
        $this->call([
            BookSeeder::class,
            BorrowSeeder::class,
        ]);
    }
}