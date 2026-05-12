<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\User;

class BookSubmissionTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('role', 'admin')->first() ?? User::first();
        
        // Create a pending book submission
        Book::create([
            'title' => 'Test Pending Book',
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'year' => 2024,
            'isbn' => 'TEST-ISBN-12345',
            'category' => 'Programming',
            'quantity' => 5,
            'available_quantity' => 5,
            'description' => 'This is a test book submission waiting for approval',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);
        
        echo "Test pending book created successfully!\n";
    }
}
