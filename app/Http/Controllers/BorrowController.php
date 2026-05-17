<?php
// app/Http/Controllers/BorrowController.php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\BorrowExtension;
use App\Models\Activity;
use App\Models\SystemParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BorrowController extends Controller
{
    /**
     * Helper untuk cek apakah user adalah petugas atau admin
     */
    private function isLibrarian($user)
    {
        return in_array($user->role, ['petugas', 'admin']);
    }

    /**
     * User: Mengajukan peminjaman buku
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Cek apakah user sedang dibatasi meminjam
        if ($user->borrow_restricted) {
            return back()->withErrors(['error' => 'Anda sedang dibatasi untuk meminjam buku.']);
        }

        // Cek apakah user punya peminjaman terlambat
        $hasOverdue = Borrow::where('user_id', $user->id)
            ->whereIn('status', ['borrowed', 'approved'])
            ->where('due_date', '<', now())
            ->exists();

        if ($hasOverdue) {
            return back()->withErrors(['error' => 'Anda memiliki peminjaman yang terlambat. Harap kembalikan terlebih dahulu.']);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500'
        ]);

        $book = Book::findOrFail($request->book_id);

        // Cek stok (gunakan calculated available quantity)
        if ($book->calculated_available_quantity < $request->quantity) {
            return back()->withErrors(['error' => 'Stok buku tidak mencukupi.']);
        }

        // Cek apakah user sudah meminjam buku yang sama (pending/approved/borrowed)
        $existingBorrow = Borrow::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->exists();

        if ($existingBorrow) {
            return back()->withErrors(['error' => 'Anda masih memiliki peminjaman aktif untuk buku ini.']);
        }

        // Cek batas maksimal peminjaman (maks 3 buku)
        $activeBorrows = Borrow::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->count();

        if ($activeBorrows >= 3) {
            return back()->withErrors(['error' => 'Anda sudah mencapai batas maksimal peminjaman (3 buku).']);
        }

        // Buat pengajuan peminjaman dengan status 'pending'
        $borrow = Borrow::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrow_date' => $request->borrow_date,
            'due_date' => $request->due_date,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Log activity untuk user
        Activity::log(Auth::id(), 'borrow_request', "Mengajukan peminjaman buku: {$book->title}");

        return back()->with('success', 'Pengajuan peminjaman berhasil dikirim. Silakan tunggu persetujuan petugas.');
    }

    /**
     * Petugas/Admin: Menyetujui peminjaman
     */
    public function approve(Borrow $borrow)
    {
        $user = Auth::user();
        
        // Gunakan helper method untuk cek role
        if (!$this->isLibrarian($user)) {
            abort(403, 'Unauthorized');
        }

        if ($borrow->status !== 'pending') {
            return back()->withErrors(['error' => 'Peminjaman ini sudah diproses sebelumnya.']);
        }

        $book = $borrow->book;

        // Cek stok buku (gunakan calculated available quantity)
        if ($book->calculated_available_quantity < $borrow->quantity) {
            return back()->withErrors(['error' => 'Stok buku tidak mencukupi.']);
        }

        // Update status peminjaman (no need to decrement available_quantity, it's calculated dynamically)
        $borrow->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Peminjaman berhasil disetujui.');
    }

    /**
     * Petugas/Admin: Menolak peminjaman
     */
    public function reject(Request $request, Borrow $borrow)
    {
        $user = Auth::user();
        
        if (!$this->isLibrarian($user)) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $borrow->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Peminjaman berhasil ditolak.');
    }

    /**
     * Petugas/Admin: Konfirmasi pengambilan buku (status approved -> borrowed)
     */
    public function confirmPickup(Borrow $borrow)
    {
        $user = Auth::user();
        
        if (!$this->isLibrarian($user)) {
            abort(403, 'Unauthorized');
        }

        if ($borrow->status !== 'approved') {
            return back()->withErrors(['error' => 'Status peminjaman tidak valid.']);
        }

        $borrow->update([
            'status' => 'borrowed',
            'borrow_date' => now(),
        ]);

        // Log activity untuk user
        Activity::log($borrow->user_id, 'borrow_pickup', "Mengambil buku: {$borrow->book->title}");

        return back()->with('success', 'Buku berhasil diambil oleh peminjam.');
    }

    /**
     * Mengembalikan buku
     * User hanya bisa kembalikan bukunya sendiri
     * Petugas/Admin bisa kembalikan buku siapa saja
     */
    public function returnBook(Borrow $borrow)
    {
        $user = Auth::user();
        
        // User biasa hanya bisa kembalikan peminjamannya sendiri
        if (!$this->isLibrarian($user) && $borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($borrow->status, ['approved', 'borrowed'])) {
            return back()->withErrors(['error' => 'Status peminjaman tidak valid untuk pengembalian.']);
        }

        $returnDate = now();
        $lateDays = 0;
        $lateFee = 0;

        // Hitung keterlambatan
        if ($returnDate->gt($borrow->due_date)) {
            $lateDays = (int) $borrow->due_date->startOfDay()->diffInDays($returnDate->startOfDay());
            $finePerDay = SystemParameter::getByKey('fine_per_day', 5000);
            $lateFee = $lateDays * $finePerDay;
        }

        // Update peminjaman
        $borrow->update([
            'status' => 'returned',
            'return_date' => $returnDate,
            'late_days' => $lateDays,
            'late_fee' => $lateFee,
        ]);

        // No need to increment available_quantity - it's calculated dynamically based on active borrows

        // Log activity untuk user
        $logMessage = $lateDays > 0 
            ? "Mengembalikan buku: {$borrow->book->title} (Terlambat {$lateDays} hari, Denda: Rp " . number_format($lateFee, 0, ',', '.') . ")"
            : "Mengembalikan buku: {$borrow->book->title} (Tepat waktu)";
        Activity::log($borrow->user_id, 'borrow_return', $logMessage);

        return back()->with('success', $lateDays > 0 
            ? "Buku berhasil dikembalikan. Terlambat {$lateDays} hari. Denda: Rp " . number_format($lateFee, 0, ',', '.')
            : 'Buku berhasil dikembalikan tepat waktu.'
        );
    }

    /**
     * User: Melihat daftar peminjaman saya
     */
    public function myLoans()
    {
        $user = Auth::user();

        $loans = Borrow::with(['book', 'approvedBy'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($loan) {
                if ($loan->isOverdue()) {
                    $loan->calculateLateFee();
                }

                $damageImages = [];
                if ($loan->damage_images) {
                    $damageImages = array_map(function($path) {
                        return asset('storage/' . $path);
                    }, json_decode($loan->damage_images, true) ?? []);
                }

                return [
                    'id' => $loan->id,
                    'book_id' => $loan->book_id,
                    'user_id' => $loan->user_id,
                    'quantity' => $loan->quantity,
                    'status' => $loan->status,
                    'status_text' => ucfirst($loan->status),
                    'borrow_date' => $loan->borrow_date,
                    'due_date' => $loan->due_date,
                    'return_date' => $loan->return_date,
                    'late_days' => $loan->late_days ?? 0,
                    'late_fee' => $loan->late_fee ?? 0,
                    'notes' => $loan->notes,
                    'approved_at' => $loan->approved_at,
                    'rejected_at' => $loan->rejected_at,
                    'rejection_reason' => $loan->rejection_reason,
                    'created_at' => $loan->created_at,
                    // Damage report details
                    'is_damaged' => $loan->is_damaged,
                    'damage_type' => $loan->damage_type,
                    'damage_description' => $loan->damage_description,
                    'damage_status' => $loan->damage_status, // pending, responded, resolved
                    'damage_images' => $damageImages,
                    'additional_fine' => $loan->additional_fine ?? 0,
                    'staff_comment' => $loan->staff_comment,
                    'payment_method' => $loan->payment_method,
                    'payment_status' => $loan->payment_status,
                    'book' => [
                        'id' => $loan->book->id,
                        'title' => $loan->book->title,
                        'author' => $loan->book->author,
                        'publisher' => $loan->book->publisher,
                        'cover_url' => $loan->book->cover_image ? asset('storage/' . $loan->book->cover_image) : null,
                    ]
                ];
            });

        return Inertia::render('User/Rak/MyLoans', [
            'loans' => $loans,
        ]);
    }

    /**
     * Melihat bukti peminjaman (detail)
     */
    // Di BorrowController.php, method show()
public function show(Borrow $borrow)
{
    $user = Auth::user();

    // User biasa hanya bisa lihat peminjamannya sendiri
    if (!$this->isLibrarian($user) && $borrow->user_id !== $user->id) {
        abort(403, 'Unauthorized');
    }

    $borrow->load(['user', 'book', 'approvedBy']);

    // Hitung denda real-time
    $lateDays = 0;
    $lateFee = 0;
    if ($borrow->isOverdue()) {
        $borrow->calculateLateFee();
        $lateDays = $borrow->late_days;
        $lateFee = $borrow->late_fee;
    } elseif ($borrow->return_date) {
        $lateDays = $borrow->late_days ?? 0;
        $lateFee = $borrow->late_fee ?? 0;
    }

    $borrowData = [
        'id' => $borrow->id,
        'book_id' => $borrow->book_id,
        'user_id' => $borrow->user_id,
        'quantity' => $borrow->quantity,
        'status' => $borrow->status,
        'status_text' => ucfirst($borrow->status),
        'borrow_date' => $borrow->borrow_date,
        'due_date' => $borrow->due_date,
        'return_date' => $borrow->return_date,
        'late_days' => $lateDays,
        'late_fee' => $lateFee,
        'payment_method' => $borrow->payment_method,
        'payment_status' => $borrow->payment_status,
        'is_damaged' => $borrow->is_damaged,
        'damage_status' => $borrow->damage_status,
        'damage_type' => $borrow->damage_type,
        'damage_description' => $borrow->damage_description,
        'additional_fine' => $borrow->additional_fine ?? 0,
        'staff_comment' => $borrow->staff_comment,
        'notes' => $borrow->notes,
        'approved_at' => $borrow->approved_at,
        'rejected_at' => $borrow->rejected_at,
        'rejection_reason' => $borrow->rejection_reason,
        'created_at' => $borrow->created_at,
        'member' => [
            'name' => $borrow->user->name,
        ],
        'book' => [
            'id' => $borrow->book->id,
            'title' => $borrow->book->title,
            'author' => $borrow->book->author,
            'publisher' => $borrow->book->publisher,
            'cover_url' => $borrow->book->cover_image ? asset('storage/' . $borrow->book->cover_image) : null,
        ]
    ];

    return Inertia::render('User/BorrowDetail', [
        'borrow' => $borrowData,
    ]);
}

    /**
     * Petugas/Admin: Melihat semua peminjaman
     * Method ini tidak digunakan karena sudah ada di PetugasController
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->isLibrarian($user)) {
            abort(403, 'Unauthorized');
        }

        $query = Borrow::with(['user', 'book', 'approvedBy']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%");
                })->orWhereHas('book', function($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%")
                      ->orWhere('isbn', 'like', "%{$search}%");
                });
            });
        }

        $borrows = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'activeCount' => Borrow::whereIn('status', ['approved', 'borrowed'])->count(),
            'overdueCount' => Borrow::whereIn('status', ['approved', 'borrowed'])
                ->where('due_date', '<', now())
                ->count(),
            'pendingCount' => Borrow::where('status', 'pending')->count(),
            'totalBorrowers' => Borrow::distinct('user_id')->count('user_id'),
        ];

        return Inertia::render('Petugas/Borrows', [
            'borrows' => $borrows,
            'stats' => $stats,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * User: Mengajukan perpanjangan peminjaman (requires approval)
     */
    public function extend(Request $request, Borrow $borrow)
    {
        $user = Auth::user();

        if ($borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($borrow->status !== 'borrowed') {
            return back()->withErrors(['error' => 'Hanya peminjaman yang sedang berjalan yang dapat diperpanjang.']);
        }

        // Get system settings
        $maxExtensions = SystemParameter::getByKey('max_extensions', 2);
        $extensionDays = SystemParameter::getByKey('extension_days', 7);

        // Check if already at max extensions
        if ($borrow->extension_count >= $maxExtensions) {
            return back()->withErrors(['error' => "Anda sudah mencapai batas maksimal perpanjangan ({$maxExtensions} kali)."]);
        }

        $request->validate([
            'requested_days' => 'required|integer|min:1|max:' . $extensionDays,
            'reason' => 'nullable|string|max:500',
        ]);

        // Check if already has pending extension request
        $existingRequest = BorrowExtension::where('borrow_id', $borrow->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return back()->withErrors(['error' => 'Anda sudah memiliki permintaan perpanjangan yang menunggu persetujuan.']);
        }

        // Create extension request
        $extension = BorrowExtension::create([
            'borrow_id' => $borrow->id,
            'status' => 'pending',
            'requested_days' => $request->requested_days,
            'reason' => $request->reason,
        ]);

        Activity::log(Auth::id(), 'borrow_extension_request', "Mengajukan perpanjangan peminjaman buku: {$borrow->book->title}");

        return back()->with('success', 'Permintaan perpanjangan berhasil dikirim. Silakan tunggu persetujuan petugas.');
    }

    /**
     * Petugas/Admin: Menyetujui perpanjangan peminjaman
     */
    public function approveExtension(BorrowExtension $extension)
    {
        $user = Auth::user();
        
        if (!$this->isLibrarian($user)) {
            abort(403, 'Unauthorized');
        }

        if ($extension->status !== 'pending') {
            return back()->withErrors(['error' => 'Permintaan perpanjangan ini sudah diproses sebelumnya.']);
        }

        $borrow = $extension->borrow;

        if ($borrow->status !== 'borrowed') {
            return back()->withErrors(['error' => 'Status peminjaman tidak valid untuk perpanjangan.']);
        }

        // Extend the due date
        $newDueDate = $borrow->due_date->addDays($extension->requested_days);

        $extension->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $borrow->update([
            'due_date' => $newDueDate,
            'extension_count' => $borrow->extension_count + 1,
        ]);

        Activity::log($borrow->user_id, 'borrow_extension_approved', "Perpanjangan peminjaman buku disetujui hingga {$newDueDate->format('d/m/Y')}");

        return back()->with('success', 'Perpanjangan peminjaman berhasil disetujui hingga ' . $newDueDate->format('d/m/Y'));
    }

    /**
     * Petugas/Admin: Menolak perpanjangan peminjaman
     */
    public function rejectExtension(Request $request, BorrowExtension $extension)
    {
        $user = Auth::user();
        
        if (!$this->isLibrarian($user)) {
            abort(403, 'Unauthorized');
        }

        if ($extension->status !== 'pending') {
            return back()->withErrors(['error' => 'Permintaan perpanjangan ini sudah diproses sebelumnya.']);
        }

        $extension->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Activity::log($extension->borrow->user_id, 'borrow_extension_rejected', 'Perpanjangan peminjaman buku ditolak');

        return back()->with('success', 'Permintaan perpanjangan berhasil ditolak.');
    }

    /**
     * User: Batalkan peminjaman (hanya untuk yang pending)
     */
    public function cancel(Borrow $borrow)
    {
        $user = Auth::user();

        if ($borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($borrow->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya pengajuan yang menunggu yang dapat dibatalkan.']);
        }

        $borrow->delete();

        return back()->with('success', 'Pengajuan peminjaman berhasil dibatalkan.');
    }

    /**
     * User: Halaman bayar denda dengan opsi QRIS dan tunai
     */
    public function payFine(Borrow $borrow)
    {
        $user = Auth::user();

        if ($borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Calculate late fee if overdue
        if ($borrow->isOverdue()) {
            $borrow->calculateLateFee();
        }

        if ($borrow->is_damaged === null) {
            return redirect()->route('fine.report-damage', ['borrow' => $borrow->id])
                ->withErrors(['error' => 'Laporkan kondisi buku terlebih dahulu sebelum memilih metode pembayaran.']);
        }

        // If book was damaged and reported, only allow payment after staff response
        if ($borrow->is_damaged && $borrow->damage_status === 'pending') {
            return back()->withErrors(['error' => 'Laporan kerusakan Anda masih dalam proses verifikasi. Silakan tunggu respons dari petugas.']);
        }

        if ($borrow->payment_status === 'pending') {
            return redirect()->route('fine.waiting', ['borrow' => $borrow->id]);
        }

        $totalFine = $borrow->late_fee + ($borrow->additional_fine ?? 0);
        
        if ($totalFine <= 0) {
            return back()->withErrors(['error' => 'Tidak ada denda untuk dibayar.']);
        }

        return Inertia::render('User/Fine/PayFine', [
            'borrow' => [
                'id' => $borrow->id,
                'book_id' => $borrow->book_id,
                'late_days' => $borrow->late_days,
                'late_fee' => $borrow->late_fee,
                'additional_fine' => $borrow->additional_fine ?? 0,
                'total_fine' => $totalFine,
                'is_damaged' => $borrow->is_damaged,
                'damage_status' => $borrow->damage_status,
                'damage_type' => $borrow->damage_type,
                'staff_comment' => $borrow->staff_comment,
                'book' => [
                    'id' => $borrow->book->id,
                    'title' => $borrow->book->title,
                    'author' => $borrow->book->author,
                ],
                'user' => [
                    'id' => $borrow->user->id,
                    'name' => $borrow->user->name,
                    'email' => $borrow->user->email,
                ]
            ],
        ]);
    }

    /**
     * User: Proses pembayaran denda (QRIS atau tunai)
     */
    public function processPayment(Request $request, Borrow $borrow)
    {
        $user = Auth::user();

        if ($borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'payment_method' => 'required|in:qris,cash',
        ]);

        if ($borrow->isOverdue()) {
            $borrow->calculateLateFee();
        }

        if ($borrow->is_damaged && $borrow->damage_status === 'pending') {
            return back()->withErrors(['error' => 'Laporan kerusakan masih menunggu tanggapan petugas.']);
        }

        $totalFine = $borrow->late_fee + ($borrow->additional_fine ?? 0);
        $paymentDueDate = now()->addHours(2);

        if ($request->payment_method === 'qris') {
            // Update borrow with QRIS payment details
            $borrow->update([
                'payment_method' => 'qris',
                'payment_status' => 'pending',
                'payment_due_date' => $paymentDueDate,
            ]);
            
            Activity::log($borrow->user_id, 'fine_payment_qris', "Mengajukan pembayaran denda via QRIS sebesar Rp " . number_format($totalFine, 0, ',', '.'));
            
            return redirect()->route('fine.qris', ['borrow' => $borrow->id]);
        } else if ($request->payment_method === 'cash') {
            // Mark as pending cash payment (akan dikonfirmasi oleh petugas)
            $borrow->update([
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'payment_due_date' => $paymentDueDate,
            ]);

            Activity::log($borrow->user_id, 'fine_payment_cash', "Mengajukan pembayaran denda tunai sebesar Rp " . number_format($totalFine, 0, ',', '.'));

            return redirect()->route('fine.waiting', ['borrow' => $borrow->id])
                ->with('success', 'Pembayaran denda tunai berhasil diajukan. Harap bayar ke petugas perpustakaan.');
        }

        return back()->withErrors(['error' => 'Metode pembayaran tidak valid.']);
    }

    /**
     * Halaman bukti peminjaman untuk print
     */
    public function proof(Borrow $borrow)
    {
        $user = Auth::user();

        if ($borrow->user_id !== $user->id && !in_array($user->role, ['petugas', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $borrow->load(['user', 'book', 'approvedBy']);

        return Inertia::render('User/BorrowProof', [
            'borrow' => $borrow,
        ]);
    }

    /**
     * Alias untuk print proof
     */
    public function printProof(Borrow $borrow)
    {
        return $this->proof($borrow);
    }

    /**
     * Petugas/Admin: Konfirmasi pembayaran denda tunai
     */
    public function confirmCashPayment(Borrow $borrow)
    {
        $user = Auth::user();

        if (!$this->isLibrarian($user)) {
            abort(403, 'Unauthorized');
        }

        if ($borrow->payment_method !== 'cash' || $borrow->payment_status !== 'pending') {
            return back()->withErrors(['error' => 'Pembayaran ini tidak valid untuk dikonfirmasi.']);
        }

        $borrow->update([
            'payment_status' => 'confirmed',
            'payment_date' => now(),
        ]);

        // If there was damage reported, mark it as ready to be marked resolved
        if ($borrow->is_damaged && $borrow->damage_status === 'responded') {
            Activity::log($borrow->user_id, 'fine_payment_confirmed', 'Pembayaran denda tunai dikonfirmasi oleh petugas - Kerusakan siap ditandai selesai');
        } else {
            Activity::log($borrow->user_id, 'fine_payment_confirmed', 'Pembayaran denda tunai dikonfirmasi oleh petugas');
        }

        return back()->with('success', 'Pembayaran denda tunai berhasil dikonfirmasi.');
    }

    public function confirmQrisPayment(Borrow $borrow)
    {
        $user = Auth::user();

        if (!$this->isLibrarian($user)) {
            abort(403, 'Unauthorized');
        }

        if ($borrow->payment_method !== 'qris' || $borrow->payment_status !== 'pending') {
            abort(403, 'Pembayaran ini tidak valid untuk dikonfirmasi.');
        }

        $borrow->update([
            'payment_status' => 'confirmed',
            'payment_date' => now(),
        ]);

        // If there was damage reported, mark it as ready to be marked resolved by staff
        if ($borrow->is_damaged && $borrow->damage_status === 'responded') {
            Activity::log($borrow->user_id, 'fine_payment_confirmed', 'Pembayaran denda QRIS dikonfirmasi - Kerusakan siap ditandai selesai');
        } else {
            Activity::log($borrow->user_id, 'fine_payment_confirmed', 'Pembayaran denda QRIS dikonfirmasi');
        }

        return back()->with('success', 'Pembayaran QRIS berhasil dikonfirmasi.');
    }

    /**
     * Admin/Petugas: Tampilkan daftar extension requests yang pending
     */
    public function extensionRequests(Request $request)
    {
        $user = Auth::user();

        if (!$this->isLibrarian($user)) {
            abort(403, 'Unauthorized');
        }

        $query = BorrowExtension::with(['borrow', 'borrow.user', 'borrow.book', 'approvedBy'])
            ->where('status', 'pending');

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('borrow.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('borrow.book', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $extensions = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/BorrowExtensions/Index', [
            'extensions' => $extensions,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * User: Tampilkan halaman QRIS payment
     */
    public function showQrisPayment(Borrow $borrow)
    {
        $user = Auth::user();

        if ($borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($borrow->payment_method !== 'qris' || $borrow->payment_status !== 'pending') {
            return back()->withErrors(['error' => 'Tidak ada pembayaran QRIS yang tertunda.']);
        }

        if ($borrow->payment_due_date && now()->gt($borrow->payment_due_date)) {
            $borrow->update([
                'payment_method' => 'none',
                'payment_status' => 'unpaid',
                'payment_due_date' => null,
            ]);

            return redirect()->route('fine.pay', ['borrow' => $borrow->id])
                ->withErrors(['error' => 'Batas waktu pembayaran habis. Silakan pilih metode pembayaran lagi.']);
        }

        $borrow->load(['user', 'book']);
        $totalFine = $borrow->late_fee + ($borrow->additional_fine ?? 0);

        return Inertia::render('User/Fine/QrisPayment', [
            'borrow' => [
                'id' => $borrow->id,
                'late_fee' => $borrow->late_fee,
                'additional_fine' => $borrow->additional_fine ?? 0,
                'total_fine' => $totalFine,
                'late_days' => $borrow->late_days,
                'payment_due_date' => $borrow->payment_due_date,
                'is_damaged' => $borrow->is_damaged,
                'damage_status' => $borrow->damage_status,
                'damage_type' => $borrow->damage_type,
                'staff_comment' => $borrow->staff_comment,
                'book' => [
                    'id' => $borrow->book->id,
                    'title' => $borrow->book->title,
                    'author' => $borrow->book->author,
                ]
            ],
            'qrisCode' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=SKIEL-LIBRARY-FINE-' . $borrow->id . '-' . time(),
        ]);
    }

public function showDamageReport(Borrow $borrow)
{
    $user = Auth::user();
    
    if ($borrow->user_id !== $user->id) {
        abort(403, 'Unauthorized');
    }
    if ($borrow->isOverdue()) {
        $borrow->calculateLateFee();
    }

    return Inertia::render('User/Fine/ReportDamage', [
        'borrow' => [
            'id' => $borrow->id,
            'late_days' => $borrow->late_days,
            'late_fee' => $borrow->late_fee,
            'book' => [
                'id' => $borrow->book->id,
                'title' => $borrow->book->title,
                'author' => $borrow->book->author,
            ]
        ]
    ]);
}

/**
 * User: Submit laporan kerusakan
 */
// Di BorrowController.php

/**
 * User: Submit laporan kerusakan
 */
public function reportDamage(Request $request, Borrow $borrow)
{
    $user = Auth::user();
    
    if ($borrow->user_id !== $user->id) {
        abort(403, 'Unauthorized');
    }

    if ($borrow->isOverdue()) {
        $borrow->calculateLateFee();
    }

    // Accept string '0'/'1' from FormData, convert to boolean
    $isDamaged = in_array($request->is_damaged, ['true', '1', true], true);
    
    // Validate based on whether book is damaged or not
    $rules = [
        'images' => 'required|array|min:1|max:3',
        'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
    ];
    
    // Only require damage details if damaged
    if ($isDamaged) {
        $rules['damage_type'] = 'required|in:lost,wet,torn,stained,other';
        $rules['damage_description'] = 'required|string|min:10|max:1000';
    }
    
    $messages = [
        'images.required' => 'Foto kondisi buku wajib diupload.',
        'images.min' => 'Minimal upload 1 foto.',
        'images.*.image' => 'File harus berupa gambar.',
        'images.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
        'images.*.max' => 'Ukuran foto maksimal 2MB.',
        'damage_type.required' => 'Pilih jenis kerusakan.',
        'damage_description.required' => 'Deskripsi kerusakan wajib diisi.',
        'damage_description.min' => 'Deskripsi minimal 10 karakter.',
    ];
    
    $validated = $request->validate($rules, $messages);

    // Store images
    $imagePaths = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('damage-reports', 'public');
            $imagePaths[] = $path;
        }
    }

    // Update borrow record
    $borrow->update([
        'is_damaged' => $isDamaged,
        'damage_type' => $isDamaged ? $request->damage_type : null,
        'damage_description' => $isDamaged ? $request->damage_description : null,
        'damage_images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
        'damage_status' => $isDamaged ? 'pending' : 'resolved',
        'payment_method' => 'none',
        'payment_status' => 'unpaid',
        'payment_due_date' => null,
    ]);

    // Log activity
    Activity::log($user->id, 'damage_report', "Melaporkan kondisi buku: {$borrow->book->title}");

    // Return JSON response for fetch API
    return response()->json([
        'success' => true,
        'message' => $isDamaged 
            ? 'Laporan kerusakan dikirim. Petugas akan review.' 
            : 'Laporan kondisi baik dikirim.'
    ]);
}

/**
 * User: Halaman menunggu pembayaran
 */
public function waitingPayment(Borrow $borrow)
{
    $user = Auth::user();
    
    if ($borrow->user_id !== $user->id) {
        abort(403, 'Unauthorized');
    }
    
    if ($borrow->payment_status !== 'pending') {
        return redirect()->route('my-loans');
    }

    if ($borrow->payment_due_date && now()->gt($borrow->payment_due_date)) {
        $borrow->update([
            'payment_method' => 'none',
            'payment_status' => 'unpaid',
            'payment_due_date' => null,
        ]);

        return redirect()->route('fine.pay', ['borrow' => $borrow->id])
            ->withErrors(['error' => 'Batas waktu pembayaran habis. Silakan pilih metode pembayaran lagi.']);
    }
    
    return Inertia::render('User/Fine/WaitingPayment', [
        'borrow' => [
            'id' => $borrow->id,
                'late_fee' => $borrow->late_fee,
                'additional_fine' => $borrow->additional_fine ?? 0,
                'total_fine' => $borrow->late_fee + ($borrow->additional_fine ?? 0),
                'payment_method' => $borrow->payment_method,
                'payment_due_date' => $borrow->payment_due_date,
                'book' => [
                'title' => $borrow->book->title,
            ]
        ]
    ]);
}

    
}
