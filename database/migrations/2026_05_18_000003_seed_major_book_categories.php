<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['Elektro', 'RPL', 'Las', 'Mesin', 'Kimia'] as $name) {
            DB::table('categories')->updateOrInsert(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => 'Kategori khusus buku jurusan ' . $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('categories')->whereIn('name', ['Elektro', 'RPL', 'Las', 'Mesin', 'Kimia'])->delete();
    }
};
