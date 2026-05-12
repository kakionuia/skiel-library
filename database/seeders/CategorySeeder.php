<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Programming', 'description' => 'Programming dan Development'],
            ['name' => 'Fiksi', 'description' => 'Buku fiksi dan novel'],
            ['name' => 'Non-Fiksi', 'description' => 'Buku non-fiksi dan referensi'],
            ['name' => 'Sejarah', 'description' => 'Buku sejarah dan biografi'],
            ['name' => 'Seni', 'description' => 'Buku seni dan desain'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
