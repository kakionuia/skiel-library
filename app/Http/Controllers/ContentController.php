public function index() {
    return response()->json([
        'artikel' => [
            ['title' => 'SMKN 1 Gunungputri rubuh, siswa diliburkan', 'category' => 'Sekolah', 'desc' => 'Deskripsi artikel 1...'],
            ['title' => 'Prestasi Siswa PPLG di Kancah Nasional', 'category' => 'Prestasi', 'desc' => 'Deskripsi artikel 2...'],
        ],
        'event' => [
            ['title' => 'Workshop UI/UX Civilearn 2026', 'category' => 'Workshop', 'desc' => 'Deskripsi event 1...'],
            ['title' => 'RR.CO Go Green Event', 'category' => 'Lingkungan', 'desc' => 'Deskripsi event 2...'],
        ]
    ]);
}