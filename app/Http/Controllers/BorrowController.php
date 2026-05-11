<?php
// app/Http/Controllers/BorrowController.php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Activity;
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

        // Cek stok
        if ($book->available_quantity < $request->quantity) {
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

        // Cek stok buku
        if ($book->available_quantity < $borrow->quantity) {
            return back()->withErrors(['error' => 'Stok buku tidak mencukupi.']);
        }

        // Kurangi stok buku
        $book->decrement('available_quantity', $borrow->quantity);

        // Update status peminjaman
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
            $lateDays = $returnDate->diffInDays($borrow->due_date);
            $lateFee = $lateDays * 1000; // Rp1000 per hari
        }

        // Update peminjaman
        $borrow->update([
            'status' => 'returned',
            'return_date' => $returnDate,
            'late_days' => $lateDays,
            'late_fee' => $lateFee,
        ]);

        // Kembalikan stok buku
        $borrow->book->increment('available_quantity', $borrow->quantity);

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
    public function show(Borrow $borrow)
    {
        $user = Auth::user();

        // User biasa hanya bisa lihat peminjamannya sendiri
        if (!$this->isLibrarian($user) && $borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $borrow->load(['user', 'book', 'approvedBy']);

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
            'late_days' => $borrow->late_days ?? 0,
            'late_fee' => $borrow->late_fee ?? 0,
            'notes' => $borrow->notes,
            'approved_at' => $borrow->approved_at,
            'rejected_at' => $borrow->rejected_at,
            'rejection_reason' => $borrow->rejection_reason,
            'created_at' => $borrow->created_at,
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
     * User: Perpanjang peminjaman
     */
    public function extend(Borrow $borrow)
    {
        $user = Auth::user();

        if ($borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($borrow->status !== 'borrowed') {
            return back()->withErrors(['error' => 'Hanya peminjaman yang sedang berjalan yang dapat diperpanjang.']);
        }

        // Cek apakah sudah pernah diperpanjang (optional: maksimal 1x perpanjangan)
        $newDueDate = $borrow->due_date->addDays(7);

        $borrow->update([
            'due_date' => $newDueDate,
        ]);

        return back()->with('success', 'Peminjaman berhasil diperpanjang hingga ' . $newDueDate->format('d/m/Y'));
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
     * User: Halaman bayar denda
     */
    public function payFine(Borrow $borrow)
    {
        $user = Auth::user();

        if ($borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($borrow->late_fee <= 0) {
            return back()->withErrors(['error' => 'Tidak ada denda untuk dibayar.']);
        }

        return Inertia::render('User/PayFine', [
            'borrow' => $borrow,
        ]);
    }

    /**
     * User: Proses pembayaran denda
     */
    public function processPayment(Request $request, Borrow $borrow)
    {
        $user = Auth::user();

        if ($borrow->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // TODO: Integrasikan dengan payment gateway
        // For now, just mark as paid
        
        $borrow->update([
            'late_fee' => 0,
        ]);

        return redirect('/my-loans')->with('success', 'Denda berhasil dibayar.');
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

    
}