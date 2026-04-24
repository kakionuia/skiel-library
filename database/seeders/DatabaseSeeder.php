<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User if not exists
        User::firstOrCreate(
            ['email' => 'admin@perpus.com'],
            [
                'name' => 'Admin Perpustakaan',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'approved',
            ]
        );

        // Create Petugas User if not exists
        User::firstOrCreate(
            ['email' => 'petugas@perpus.com'],
            [
                'name' => 'Petugas Perpustakaan',
                'password' => bcrypt('password'),
                'role' => 'petugas',
                'status' => 'approved',
            ]
        );
    }
}
