<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin Perpustakaan',
                'email' => 'admin@skie-library.test',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'approved',
            ]);
        }

        // Create test articles
        Article::create([
            'title' => 'Tips Membaca Efektif',
            'slug' => 'tips-membaca-efektif',
            'content' => 'Membaca adalah keterampilan yang penting untuk semua orang. Berikut adalah tips untuk membaca lebih efektif dan meningkatkan pemahaman Anda tentang materi yang dibaca.',
            'excerpt' => 'Pelajari tips dan trik untuk membaca lebih efektif dan meningkatkan pemahaman Anda',
            'category' => 'artikel',
            'author_id' => $admin->id,
            'approved_by' => $admin->id,
            'status' => 'approved',
            'published_at' => now(),
            'approved_at' => now(),
        ]);

        Article::create([
            'title' => 'Event Literasi Bulan Ini',
            'slug' => 'event-literasi-bulan-ini',
            'content' => 'Perpustakaan SMKN 1 Gunungputri menghadirkan event literasi yang menarik bulan ini dengan berbagai kegiatan seru dan hadiah menarik untuk semua peserta.',
            'excerpt' => 'Ikuti event literasi eksklusif dengan hadiah menarik',
            'category' => 'berita',
            'author_id' => $admin->id,
            'approved_by' => $admin->id,
            'status' => 'approved',
            'published_at' => now(),
            'approved_at' => now(),
        ]);

        Article::create([
            'title' => 'Buku Terbaru di Perpustakaan',
            'slug' => 'buku-terbaru-di-perpustakaan',
            'content' => 'Koleksi buku terbaru telah tiba di perpustakaan. Koleksi ini mencakup buku-buku bestseller terbaru, buku referensi, dan buku literasi anak. Cek daftarnya di sini dan segera pinjam sebelum kehabisan.',
            'excerpt' => 'Temukan buku-buku terbaru yang baru ditambahkan ke koleksi perpustakaan',
            'category' => 'berita',
            'author_id' => $admin->id,
            'approved_by' => $admin->id,
            'status' => 'approved',
            'published_at' => now()->subDays(3),
            'approved_at' => now()->subDays(3),
        ]);

        Article::create([
            'title' => 'Manfaat Membaca Bagi Kesehatan Mental',
            'slug' => 'manfaat-membaca-bagi-kesehatan-mental',
            'content' => 'Membaca memiliki manfaat yang luar biasa untuk kesehatan mental. Dari mengurangi stress hingga meningkatkan fokus dan konsentrasi, membaca adalah aktivitas yang sangat bermanfaat untuk semua orang.',
            'excerpt' => 'Discover the mental health benefits of reading and why everyone should make it a daily habit',
            'category' => 'artikel',
            'author_id' => $admin->id,
            'approved_by' => $admin->id,
            'status' => 'approved',
            'published_at' => now()->subDays(5),
            'approved_at' => now()->subDays(5),
        ]);
    }
}
