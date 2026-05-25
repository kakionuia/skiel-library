<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;
use App\Models\SystemParameter;
use Illuminate\Database\Seeder;

class BorrowSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::orderBy('id')->get();

        if ($books->isEmpty()) {
            return;
        }

        $finePerDay = SystemParameter::getByKey('fine_per_day', 5000);
        $student = User::updateOrCreate(
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

        $activeBook = $books->first();
        $overdueBook = $books->skip(1)->first() ?? $activeBook;
        $returnedBook = $books->skip(2)->first() ?? $activeBook;

        Borrow::updateOrCreate(
            [
                'user_id' => $student->id,
                'notes' => 'DATA_TEST_SISWA_ACTIVE',
            ],
            [
                'book_id' => $activeBook->id,
                'borrow_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'return_date' => null,
                'status' => 'borrowed',
                'quantity' => 1,
                'late_days' => 0,
                'late_fee' => 0,
                'payment_method' => 'none',
                'payment_status' => 'unpaid',
            ]
        );

        Borrow::updateOrCreate(
            [
                'user_id' => $student->id,
                'notes' => 'DATA_TEST_SISWA_RETURNED',
            ],
            [
                'book_id' => $returnedBook->id,
                'borrow_date' => now()->subDays(10)->toDateString(),
                'due_date' => now()->subDays(3)->toDateString(),
                'return_date' => now()->subDay()->toDateString(),
                'status' => 'returned',
                'quantity' => 1,
                'late_days' => 0,
                'late_fee' => 0,
                'payment_method' => 'none',
                'payment_status' => 'unpaid',
            ]
        );

        $lateDays = 5;
        Borrow::updateOrCreate(
            [
                'user_id' => $student->id,
                'notes' => 'DATA_TEST_SISWA_OVERDUE',
            ],
            [
                'book_id' => $overdueBook->id,
                'borrow_date' => now()->subDays(12)->toDateString(),
                'due_date' => now()->subDays($lateDays)->toDateString(),
                'return_date' => null,
                'status' => 'borrowed',
                'quantity' => 1,
                'late_days' => $lateDays,
                'late_fee' => $lateDays * $finePerDay,
                'payment_method' => 'none',
                'payment_status' => 'unpaid',
            ]
        );

        for ($i = 1; $i <= 12; $i++) {
            $lateDays = 5 + $i;
            $book = $books->get($i % $books->count()) ?? $activeBook;

            Borrow::updateOrCreate(
                [
                    'user_id' => $student->id,
                    'notes' => sprintf('DATA_TEST_SISWA_OVERDUE_EXTRA_%02d', $i),
                ],
                [
                    'book_id' => $book->id,
                    'borrow_date' => now()->subDays($lateDays + 7)->toDateString(),
                    'due_date' => now()->subDays($lateDays)->toDateString(),
                    'return_date' => null,
                    'status' => 'borrowed',
                    'quantity' => 1,
                    'late_days' => $lateDays,
                    'late_fee' => $lateDays * $finePerDay,
                    'payment_method' => 'none',
                    'payment_status' => 'unpaid',
                ]
            );
        }

        $extraUsers = User::where('role', 'anggota')
            ->where('id', '!=', $student->id)
            ->orderBy('id')
            ->get();

        if ($extraUsers->isEmpty()) {
            return;
        }

        Borrow::updateOrCreate([
            'user_id' => $extraUsers->first()->id,
            'notes' => 'DATA_TEST_EXTRA_ACTIVE',
        ], [
            'book_id' => $activeBook->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
            'quantity' => 1,
        ]);
    }
}
