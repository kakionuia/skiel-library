<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BorrowSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil semua buku dan user yang ada
        $books = Book::all();
        $users = User::where('role', 'anggota')->get();

        // 2. Cek dulu, kalau bukunya atau usernya kosong, jangan lanjut biar gak error
        if ($books->isEmpty() || $users->isEmpty()) {
            return;
        }

        // 3. Buat data peminjaman secara aman
        Borrow::create([
            'user_id' => $users->random()->id, // Ambil user acak
            'book_id' => $books->random()->id, // Ambil buku acak
            'borrow_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'borrowed',
        ]);

        // Tambah satu lagi yang sudah kembali
        Borrow::create([
            'user_id' => $users->first()->id, 
            'book_id' => $books->first()->id,
            'borrow_date' => now()->subDays(10),
            'due_date' => now()->subDays(3),
            'return_date' => now()->subDay(),
            'status' => 'returned',
        ]);
    }
}