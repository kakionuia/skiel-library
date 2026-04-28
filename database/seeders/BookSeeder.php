<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cari user Admin yang baru saja dibuat di DatabaseSeeder
        $admin = User::where('role', 'admin')->first();

        // Jaga-jaga kalau admin tidak ditemukan (meskipun harusnya ada)
        $adminId = $admin ? $admin->id : 1;

        // 2. Masukkan data buku dengan semua kolom yang diminta database
        Book::firstOrCreate(
            ['isbn' => '978-0-13-110362-7'],
            [
                'book_id' => 'BK-' . strtoupper(Str::random(8)),
                'title' => 'The C Programming Language',
                'author' => 'Brian W. Kernighan, Dennis M. Ritchie',
                'category' => 'Programming',
                'status' => 'approved', // Pakai 'approved' sesuai logic controller kamu
                'quantity' => 3,
                'available_quantity' => 3,
                'created_by' => $adminId, // ID Admin masuk ke sini
            ]
        );

        Book::firstOrCreate(
            ['isbn' => '978-0-201-63361-0'],
            [
                'book_id' => 'BK-' . strtoupper(Str::random(8)),
                'title' => 'Design Patterns',
                'author' => 'Gang of Four',
                'category' => 'Programming',
                'status' => 'approved',
                'quantity' => 2,
                'available_quantity' => 2,
                'created_by' => $adminId,
            ]
        );
        
        // Tambahkan buku lainnya dengan pola yang sama...
    }
}