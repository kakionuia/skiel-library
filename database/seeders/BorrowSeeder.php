<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;
use App\Models\SystemParameter;
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

        // Get fine per day from system parameters
        $finePerDay = SystemParameter::getByKey('fine_per_day', 5000);

        // 3. Buat data peminjaman secara aman
        // Borrow yang masih aktif (on time)
        Borrow::create([
            'user_id' => $users->random()->id,
            'book_id' => $books->random()->id,
            'borrow_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'borrowed',
        ]);

        // Borrow yang sudah kembali (tepat waktu)
        Borrow::create([
            'user_id' => $users->first()->id, 
            'book_id' => $books->first()->id,
            'borrow_date' => now()->subDays(10),
            'due_date' => now()->subDays(3),
            'return_date' => now()->subDay(),
            'status' => 'returned',
        ]);

        // Borrow yang TERLAMBAT 5 hari (dummy data untuk testing)
        if ($users->count() >= 2) {
            $lateDays = 5;
            Borrow::create([
                'user_id' => $users->get(1)->id,
                'book_id' => $books->get(1)->id ?? $books->first()->id,
                'borrow_date' => now()->subDays(12),
                'due_date' => now()->subDays($lateDays),
                'status' => 'borrowed',
                'late_days' => 0, // Will be calculated dynamically
                'late_fee' => 0, // Will be calculated dynamically
            ]);
        }

        // Borrow yang TERLAMBAT 10 hari (dummy data untuk testing)
        if ($users->count() >= 3) {
            $lateDays = 10;
            Borrow::create([
                'user_id' => $users->get(2)->id,
                'book_id' => $books->get(2)->id ?? $books->first()->id,
                'borrow_date' => now()->subDays(17),
                'due_date' => now()->subDays($lateDays),
                'status' => 'borrowed',
                'late_days' => 0, // Will be calculated dynamically
                'late_fee' => 0, // Will be calculated dynamically
            ]);
        }

        // Borrow yang sudah kembali TERLAMBAT 7 hari (dummy data untuk testing)
        if ($users->count() >= 2 && $books->count() >= 3) {
            $lateDays = 7;
            $lateFee = $lateDays * $finePerDay;
            Borrow::create([
                'user_id' => $users->get(1)->id,
                'book_id' => $books->get(2)->id,
                'borrow_date' => now()->subDays(14),
                'due_date' => now()->subDays(7),
                'return_date' => now()->subDays(1),
                'status' => 'returned',
                'late_days' => $lateDays,
                'late_fee' => $lateFee,
            ]);
        }
    }
}