<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cari user Admin yang baru saja dibuat di DatabaseSeeder
        $admin = User::where('role', 'admin')->first();

        // Jaga-jaga kalau admin tidak ditemukan (meskipun harusnya ada)
        $adminId = $admin ? $admin->id : 1;

        // 2. Masukkan data buku dengan semua kolom yang diminta database
        Book::updateOrCreate(
            ['isbn' => '978-0-13-110362-7'],
            [
                'book_id' => 'BK-C-PROGRAMMING',
                'title' => 'The C Programming Language',
                'author' => 'Brian W. Kernighan, Dennis M. Ritchie',
                'barcode_code' => '400/SKIE/thecprogramminglanguage',
                'category' => 'Programming',
                'status' => 'approved', // Pakai 'approved' sesuai logic controller kamu
                'quantity' => 3,
                'available_quantity' => 3,
                'created_by' => $adminId, // ID Admin masuk ke sini
            ]
        );

        Book::updateOrCreate(
            ['isbn' => '978-0-201-63361-0'],
            [
                'book_id' => 'BK-DESIGN-PATTERNS',
                'title' => 'Design Patterns',
                'author' => 'Gang of Four',
                'barcode_code' => '400/SKIE/designpatterns',
                'category' => 'Programming',
                'status' => 'approved',
                'quantity' => 2,
                'available_quantity' => 2,
                'created_by' => $adminId,
            ]
        );

        Book::updateOrCreate(
            ['isbn' => '978-602-452-698-6'],
            [
                'book_id' => 'BK-SENI-BERSIKAP',
                'title' => 'Seni Bersikap Bodo Amat',
                'author' => 'Mark Manson',
                'barcode_code' => '400/SKIE/senibersikapbodoamat',
                'category' => 'Non Fiksi',
                'status' => 'approved',
                'quantity' => 20,
                'available_quantity' => 20,
                'created_by' => $adminId,
            ]
        );
        
        // Tambahkan buku lainnya dengan pola yang sama...
    }
}
