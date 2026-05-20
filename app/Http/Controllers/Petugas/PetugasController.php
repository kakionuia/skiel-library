<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\BookRequest;
use App\Models\Category;
use App\Models\SystemParameter;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $totalBooks = Book::where('status', 'approved')->count();
        $activeLoans = Borrow::where('status', 'borrowed')->count();
        $todayReturns = Borrow::whereDate('return_date', now()->toDateString())->count();
        $lateCount = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();
        $totalFines = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->sum('late_fee') ?? 0;
        
        // My submissions
        $mySubmissions = [
            'total' => Book::where('created_by', Auth::id())->count(),
            'pending' => Book::where('created_by', Auth::id())->where('status', 'pending')->count(),
            'approved' => Book::where('created_by', Auth::id())->where('status', 'approved')->count(),
            'rejected' => Book::where('created_by', Auth::id())->where('status', 'rejected')->count(),
        ];
        
        // Recent activities
        $recentActivities = Activity::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return inertia('Petugas/Dashboard', [
            'stats' => [
                'activeLoans' => $activeLoans,
                'todayReturns' => $todayReturns,
                'totalBooks' => $totalBooks,
                'totalFines' => $totalFines,
            ],
            'mySubmissions' => $mySubmissions,
            'recentActivities' => $recentActivities,
            'lateCount' => $lateCount,
        ]);
    }

    // Books Management
    public function books()
    {
        // Approved books (Koleksi Tersedia)
        $approvedBooks = Book::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'category' => $book->category,
                    'status' => 'available',
                    'total' => $book->quantity ?? 0,
                    'available' => $book->available_quantity ?? 0,
                    'cover' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
                ];
            });

        // My pending submissions (Menunggu Approval)
        $pendingBooks = Book::where('created_by', Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'category' => $book->category,
                    'status' => 'pending',
                    'total' => $book->quantity ?? 0,
                    'available' => $book->available_quantity ?? 0,
                    'cover' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
                ];
            });

        // Combine books for display using array_merge
        $books = array_merge($approvedBooks->toArray(), $pendingBooks->toArray());

        return inertia('Petugas/Books/Index', [
            'books' => $books,
        ]);
    }

    public function createBook()
    {
        return inertia('Petugas/Books/Create', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storeBook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2100',
            'isbn' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('books', 'public');
        }

        $book = Book::create([
            'book_id' => 'BK-' . strtoupper(Str::random(8)),
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'year' => $request->year,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'quantity' => $request->quantity ?? 1,
            'available_quantity' => $request->quantity ?? 1,
            'description' => $request->description,
            'cover_image' => $coverPath,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        Activity::log(Auth::id(), 'create', "Menambahkan buku: {$book->title}");

        return redirect()->route('petugas.books')->with('success', 'Buku berhasil ditambahkan dan menunggu persetujuan admin.');
    }

    public function editBook(Book $book)
    {
        return inertia('Petugas/Books/Edit', [
            'book' => $book,
        ]);
    }

    public function updateBook(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2100',
            'isbn' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $oldQty = $book->quantity;
        $newQty = $request->quantity ?? $book->quantity;
        $qtyDiff = $newQty - $oldQty;

        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'year' => $request->year,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'quantity' => $newQty,
            // Do not update available_quantity - it's calculated dynamically based on active borrows
            'description' => $request->description,
        ]);

        Activity::log(Auth::id(), 'update', "Mengubah data buku: {$book->title}");

        return redirect()->route('petugas.books')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroyBook(Book $book)
    {
        $title = $book->title;
        $book->delete();

        Activity::log(Auth::id(), 'delete', "Menghapus buku: {$title}");

        return back()->with('success', 'Buku berhasil dihapus.');
    }

    // Book Requests
    public function bookRequests()
    {
        $requests = BookRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'user_name' => $request->user?->name ?? 'Unknown',
                    'title' => $request->title,
                    'author' => $request->author,
                    'reason' => $request->description ?? 'Tidak ada alasan',
                    'status' => $request->status,
                    'created_at' => $request->created_at,
                ];
            });

        return inertia('Petugas/BookRequests/Index', [
            'bookRequests' => $requests,
        ]);
    }

    public function storeBookRequest(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2100',
            'isbn' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
        ]);

        BookRequest::create([
            'petugas_id' => Auth::id(),
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'year' => $request->year,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'status' => 'pending',
        ]);

        Activity::log(Auth::id(), 'request_book', "Mengajukan permintaan buku: {$request->title}");

        return back()->with('success', 'Permintaan buku telah diajukan ke admin.');
    }

    // Late Returns
    public function lateReturns()
    {
        $lateBorrows = Borrow::with(['user', 'book'])
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function($borrow) {
                $borrow->calculateLateFee();
                
                return [
                    'id' => $borrow->id,
                    'user_id' => $borrow->user?->id,
                    'member_name' => $borrow->user?->name ?? 'Unknown',
                    'member_id' => $borrow->user?->nisn ?? $borrow->user?->id ?? 'N/A',
                    'book_title' => $borrow->book?->title ?? 'Unknown',
                    'borrow_date' => $borrow->borrow_date,
                    'due_date' => $borrow->due_date,
                    'days_overdue' => $borrow->late_days,
                    'fine' => (float) $borrow->late_fee,
                    'is_damaged' => $borrow->is_damaged,
                    'damage_status' => $borrow->damage_status,
                    'payment_status' => $borrow->payment_status,
                ];
            });

        $totalFines = $lateBorrows->sum('fine');
        $averageDaysOverdue = $lateBorrows->count() > 0 
            ? round($lateBorrows->avg('days_overdue'))
            : 0;

        return inertia('Petugas/LateReturns/Index', [
            'overdueLoans' => $lateBorrows,
            'totalFines' => $totalFines,
            'averageDaysOverdue' => $averageDaysOverdue,
        ]);
    }

    // Detail anggota terlambat
    public function showLateReturn(User $user)
    {
        $lateBorrows = Borrow::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_date', '<', now())
            ->with('book')
            ->get()
            ->map(function($borrow) {
                $borrow->calculateLateFee();
                
                return [
                    'id' => $borrow->id,
                    'book' => [
                        'id' => $borrow->book->id,
                        'title' => $borrow->book->title,
                        'author' => $borrow->book->author,
                    ],
                    'borrow_date' => $borrow->borrow_date,
                    'due_date' => $borrow->due_date->format('Y-m-d'),
                    'late_days' => $borrow->late_days,
                    'late_fee' => (float) $borrow->late_fee,
                    'payment_method' => $borrow->payment_method,
                    'payment_status' => $borrow->payment_status,
                    'payment_due_date' => $borrow->payment_due_date,
                    'is_damaged' => $borrow->is_damaged,
                    'damage_status' => $borrow->damage_status,
                    'damage_type' => $borrow->damage_type,
                    'damage_description' => $borrow->damage_description,
                    'damage_images' => $borrow->damage_images ? array_map(function($path) {
                        return asset('storage/' . $path);
                    }, json_decode($borrow->damage_images, true)) : [],
                    'additional_fine' => $borrow->additional_fine ?? 0,
                    'staff_comment' => $borrow->staff_comment,
                ];
            });
        
        // Hitung total
        $totalFine = $lateBorrows->sum('late_fee') + $lateBorrows->sum('additional_fine');
        $totalLateDays = $lateBorrows->sum('late_days');
        
        return inertia('Petugas/LateReturns/Show', [
            'member' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nisn' => $user->nisn,
                'kelas' => $user->kelas,
                'jurusan' => $user->jurusan,
                'phone' => $user->phone,
            ],
            'lateBorrows' => $lateBorrows,
            'totalFine' => $totalFine,
            'totalLateDays' => $totalLateDays,
        ]);
    }

    /**
     * Tambah denda tambahan karena kerusakan
     */
    public function addAdditionalFine(Request $request, Borrow $borrow)
    {
        $request->validate([
            'additional_fine' => 'required|numeric|min:0',
            'staff_comment' => 'nullable|string|max:500',
        ]);
        
        $borrow->update([
            'additional_fine' => $request->additional_fine,
            'staff_comment' => $request->staff_comment,
            'damage_status' => 'responded',
            'payment_method' => 'none',
            'payment_status' => 'unpaid',
            'payment_due_date' => null,
        ]);
        
        Activity::log(Auth::id(), 'additional_fine', "Menambah denda kerusakan buku: {$borrow->book->title} sebesar Rp " . number_format($request->additional_fine));
        
        return back()->with('success', 'Denda tambahan berhasil dikirim ke anggota.');
    }

    /**
     * Tandai masalah selesai
     */
    public function resolveLateReturn(Borrow $borrow)
    {
        $borrow->update([
            'payment_status' => 'confirmed',
            'payment_date' => now(),
        ]);
        
        // No need to increment available_quantity - it's calculated dynamically based on active borrows
        
        // Update status peminjaman menjadi returned
        $borrow->update([
            'status' => 'returned',
            'return_date' => now(),
        ]);
        
        Activity::log(Auth::id(), 'resolve_return', "Menandai masalah keterlambatan selesai untuk buku: {$borrow->book->title}");
        
        return back()->with('success', 'Masalah keterlambatan telah diselesaikan dan buku telah dikembalikan.');
    }

    // Activity Logs
    public function activityLogs()
    {
        $activities = Activity::with('user')->orderBy('created_at', 'desc')
            ->paginate(20)
            ->map(function($activity) {
                $typeMap = ['create' => 'create', 'update' => 'update', 'delete' => 'delete', 'request_book' => 'request'];
                return [
                    'id' => $activity->id,
                    'user' => $activity->user ? [
                        'id' => $activity->user->id,
                        'name' => $activity->user->name,
                        'profile_photo_url' => $activity->user->profile_photo_url,
                    ] : null,
                    'description' => $activity->description ?? 'Aktivitas',
                    'details' => $activity->metadata['details'] ?? '',
                    'action' => $typeMap[$activity->type] ?? 'process',
                    'created_at' => $activity->created_at,
                ];
            });

        return inertia('Petugas/ActivityLogs/Index', [
            'activityLogs' => $activities,
        ]);
    }

    // Borrows Management - Kelola Peminjaman
    public function borrows()
    {
        $borrows = Borrow::with(['user', 'book'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($borrow) {
                // Status text untuk display
                $statusText = match($borrow->status) {
                    'pending' => 'Menunggu Persetujuan',
                    'approved' => 'Disetujui',
                    'borrowed' => 'Aktif',
                    'returned' => 'Dikembalikan',
                    'overdue' => 'Terlambat',
                    'lost' => 'Hilang',
                    'rejected' => 'Ditolak',
                    default => $borrow->status
                };

                // DYNAMICALLY calculate late_fee for overdue borrowed items
                $lateDays = 0;
                $lateFee = 0;
                
                if (in_array($borrow->status, ['borrowed', 'approved']) && now()->gt($borrow->due_date)) {
                    $lateDays = now()->diffInDays($borrow->due_date);
                    $finePerDay = SystemParameter::getByKey('fine_per_day', 5000);
                    $lateFee = $lateDays * $finePerDay;
                } elseif ($borrow->status === 'returned') {
                    // For returned items, use the stored late_days and late_fee
                    $lateDays = $borrow->late_days ?? 0;
                    $lateFee = $borrow->late_fee ?? 0;
                }

                return [
                    'id' => $borrow->id,
                    'user_id' => $borrow->user_id,
                    'member_name' => $borrow->user?->name ?? 'Unknown',
                    'member_id' => $borrow->user?->nisn ?? $borrow->user?->id ?? 'N/A',
                    'book_title' => $borrow->book?->title ?? 'Unknown',
                    'book_isbn' => $borrow->book?->isbn ?? 'N/A',
                    'borrow_date' => $borrow->borrow_date,
                    'due_date' => $borrow->due_date,
                    'return_date' => $borrow->return_date,
                    'status' => $borrow->status,
                    'status_text' => $statusText,
                    'quantity' => $borrow->quantity ?? 1,
                    'late_days' => $lateDays,
                    'late_fee' => $lateFee,
                    'notes' => $borrow->notes,
                    'is_damaged' => $borrow->is_damaged,
                    'payment_status' => $borrow->payment_status,
                    'payment_method' => $borrow->payment_method,
                ];
            });

        // Stats
        $activeCount = Borrow::whereIn('status', ['approved', 'borrowed'])->count();
        $overdueCount = Borrow::whereIn('status', ['approved', 'borrowed'])
            ->where('due_date', '<', now())
            ->count();
        
        // Rata-rata durasi pinjaman
        $totalBorrowData = Borrow::whereIn('status', ['approved', 'borrowed'])->get();
        $avgDuration = $totalBorrowData->count() > 0
            ? round($totalBorrowData->avg(function($b) {
                return now()->diffInDays($b->borrow_date ?? $b->created_at);
            }))
            : 0;
        
        $totalBorrowers = Borrow::whereIn('status', ['approved', 'borrowed', 'returned'])
            ->distinct('user_id')
            ->count('user_id');

        return inertia('Petugas/Borrows/Index', [
            'stats' => [
                'activeCount' => $activeCount,
                'overdueCount' => $overdueCount,
                'avgDuration' => $avgDuration,
                'totalBorrowers' => $totalBorrowers,
            ],
            'borrows' => $borrows,
        ]);
    }

    // Process return method dengan perhitungan denda
    public function processReturn(Request $request, Borrow $borrow)
    {
        $request->validate([
            'return_date' => 'required|date',
        ]);

        $returnDate = Carbon::parse($request->return_date);
        $lateDays = 0;
        $lateFee = 0;

        // Hitung keterlambatan
        if ($returnDate->gt($borrow->due_date)) {
            $lateDays = $returnDate->diffInDays($borrow->due_date);
            $finePerDay = SystemParameter::getByKey('fine_per_day', 5000);
            $lateFee = $lateDays * $finePerDay;
        }

        $borrow->update([
            'return_date' => $returnDate,
            'status' => 'returned',
            'late_days' => $lateDays,
            'late_fee' => $lateFee,
        ]);

        // No need to increment available_quantity - it's calculated dynamically based on active borrows

        Activity::log(Auth::id(), 'return', "Memproses pengembalian buku: {$borrow->book->title}");

        $message = 'Buku berhasil dikembalikan.';
        if ($lateDays > 0) {
            $message .= " Terlambat {$lateDays} hari. Denda: Rp " . number_format($lateFee, 0, ',', '.');
        }

        return back()->with('success', $message);
    }

    // Konfirmasi pembayaran tunai
    public function confirmCashPayment(Borrow $borrow)
    {
        $borrow->update([
            'payment_status' => 'confirmed',
            'payment_date' => now(),
        ]);
        
        Activity::log(Auth::id(), 'confirm_payment', "Mengkonfirmasi pembayaran tunai untuk buku: {$borrow->book->title}");
        
        return back()->with('success', 'Pembayaran tunai berhasil dikonfirmasi.');
    }

    // Konfirmasi pembayaran QRIS
    public function confirmQrisPayment(Borrow $borrow)
    {
        $borrow->update([
            'payment_status' => 'confirmed',
            'payment_date' => now(),
        ]);
        
        Activity::log(Auth::id(), 'confirm_payment', "Mengkonfirmasi pembayaran QRIS untuk buku: {$borrow->book->title}");
        
        return back()->with('success', 'Pembayaran QRIS berhasil dikonfirmasi.');
    }

    // Reports - Laporan Harian
    public function reports()
    {
        $today = now()->toDateString();
        
        // Today's loans
        $todayLoans = Borrow::whereDate('borrow_date', $today)->count();
        
        // Today's returns
        $todayReturns = Borrow::whereDate('return_date', $today)->count();
        
        // Total fines today - Calculate from late fees
        $overdueLoans = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->get();
        
        $totalFines = $overdueLoans->sum(function($borrow) {
            $daysOverdue = now()->diffInDays($borrow->due_date);
            $finePerDay = SystemParameter::getByKey('fine_per_day', 5000);
            return $daysOverdue * $finePerDay;
        }) ?? 0;

        // Critical activity (overdue count)
        $criticalActivity = $overdueLoans->count();

        // Activity summary
        $activitySummary = [
            [
                'label' => 'Peminjaman',
                'value' => $todayLoans,
                'color' => 'bg-indigo-500',
            ],
            [
                'label' => 'Pengembalian',
                'value' => $todayReturns,
                'color' => 'bg-emerald-500',
            ],
            [
                'label' => 'Denda Hari Ini',
                'value' => $totalFines > 0 ? 'Rp ' . number_format($totalFines) : 'Rp 0',
                'color' => 'bg-amber-500',
            ],
        ];

        // Top members today
        $topMembers = Borrow::with('user')
            ->whereDate('borrow_date', $today)
            ->select('user_id')
            ->groupBy('user_id')
            ->get()
            ->map(function($borrow) {
                $count = Borrow::whereDate('borrow_date', now()->toDateString())
                    ->where('user_id', $borrow->user_id)
                    ->count();
                return [
                    'id' => $borrow->user_id,
                    'name' => $borrow->user?->name ?? 'Unknown',
                    'borrow_count' => $count,
                ];
            })
            ->sortByDesc('borrow_count')
            ->take(5)
            ->values();

        return inertia('Petugas/Reports/Index', [
            'todayLoans' => $todayLoans,
            'todayReturns' => $todayReturns,
            'totalFines' => $totalFines,
            'criticalActivity' => $criticalActivity,
            'activitySummary' => $activitySummary,
            'topMembers' => $topMembers,
        ]);
    }

    // Settings
    public function settings()
    {
        $parameters = SystemParameter::all();
        
        $formattedParams = [];
        foreach ($parameters as $param) {
            $formattedParams[] = [
                'id' => $param->id,
                'key' => $param->key,
                'value' => $param->value,
                'type' => $param->type,
                'description' => $param->description,
            ];
        }

        return inertia('Petugas/Settings', [
            'user' => Auth::user(),
            'parameters' => $formattedParams,
        ]);
    }

    // Book Submissions (pengajuan buku)
    public function mySubmissions()
    {
        $submissions = Book::where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'category' => $book->category,
                    'status' => $book->status,
                    'quantity' => $book->quantity,
                    'cover' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
                    'created_at' => $book->created_at,
                    'approved_at' => $book->approved_at,
                ];
            });

        $stats = [
            'total' => $submissions->count(),
            'pending' => $submissions->where('status', 'pending')->count(),
            'approved' => $submissions->where('status', 'approved')->count(),
            'rejected' => $submissions->where('status', 'rejected')->count(),
        ];

        return inertia('Petugas/Submissions/Index', [
            'submissions' => $submissions,
            'stats' => $stats,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'max_borrow_days' => 'required|integer|min:1|max:365',
            'late_fee_per_day' => 'required|integer|min:0',
            'max_books_per_member' => 'required|integer|min:1|max:100',
        ]);

        // Update SystemParameter table directly (not cache!)
        SystemParameter::setValue('max_borrow_days', $request->max_borrow_days, 'integer', 'Jumlah hari maksimal peminjaman buku');
        SystemParameter::setValue('fine_per_day', $request->late_fee_per_day, 'integer', 'Nominal denda per hari keterlambatan');
        SystemParameter::setValue('max_books_per_member', $request->max_books_per_member, 'integer', 'Jumlah maksimal buku per member');

        // Clear cache untuk memastikan nilai baru di-load
        cache()->forget('library.parameters');

        Activity::log(Auth::id(), 'update_parameter', 'Memperbarui parameter sistem: max_borrow_days=' . $request->max_borrow_days . ', fine_per_day=' . $request->late_fee_per_day);

        return back()->with('success', 'Parameter sistem berhasil diperbarui. Perubahan langsung berlaku untuk semua perhitungan.');
    }

    /**
     * Petugas: Laporan kerusakan buku yang pending
     */
    public function damageReports()
    {
        $reports = Borrow::where('is_damaged', true)
            ->where('damage_status', 'pending')
            ->with(['user', 'book'])
            ->latest()
            ->paginate(15);

        return inertia('Petugas/DamageReports/Index', [
            'reports' => $reports,
        ]);
    }

    /**
     * Petugas: Detail laporan kerusakan
     */
    public function showDamageReport(Borrow $borrow)
    {
        if ($borrow->is_damaged !== true || $borrow->damage_status !== 'pending') {
            abort(404, 'Laporan tidak ditemukan atau sudah ditanggapi.');
        }

        // Calculate late fee if overdue
        if ($borrow->isOverdue()) {
            $borrow->calculateLateFee();
        }

        $damageImages = [];
        if ($borrow->damage_images) {
            $damageImages = array_map(function($path) {
                return asset('storage/' . $path);
            }, json_decode($borrow->damage_images, true) ?? []);
        }

        return inertia('Petugas/DamageReports/Show', [
            'report' => [
                'id' => $borrow->id,
                'user' => [
                    'id' => $borrow->user->id,
                    'name' => $borrow->user->name,
                    'email' => $borrow->user->email,
                ],
                'book' => [
                    'id' => $borrow->book->id,
                    'title' => $borrow->book->title,
                    'author' => $borrow->book->author,
                ],
                'late_days' => now()->diffInDays($borrow->due_date),
                'late_fee' => $borrow->late_fee,
                'damage_type' => $borrow->damage_type,
                'damage_description' => $borrow->damage_description,
                'damage_images' => $damageImages,
                'is_damaged' => $borrow->is_damaged,
                'damage_status' => $borrow->damage_status,
                'additional_fine' => $borrow->additional_fine ?? 0,
                'staff_comment' => $borrow->staff_comment,
                'created_at' => $borrow->created_at,
            ]
        ]);
    }

    /**
     * Petugas: Respond ke laporan kerusakan
     */
    public function respondToDamageReport(Request $request, Borrow $borrow)
    {
        if ($borrow->is_damaged !== true || $borrow->damage_status !== 'pending') {
            abort(404, 'Laporan tidak ditemukan.');
        }

        $request->validate([
            'additional_fine' => 'required|integer|min:0|max:1000000',
            'staff_comment' => 'required|string|min:10|max:500',
        ], [
            'additional_fine.required' => 'Nominal denda tambahan wajib diisi.',
            'additional_fine.integer' => 'Denda harus berupa angka.',
            'staff_comment.required' => 'Komentar petugas wajib diisi.',
            'staff_comment.min' => 'Komentar minimal 10 karakter.',
        ]);

        // Update borrow dengan response dari petugas
        $borrow->update([
            'additional_fine' => $request->additional_fine,
            'staff_comment' => $request->staff_comment,
            'damage_status' => 'responded', // Now user can see response
            'payment_method' => 'none',
            'payment_status' => 'unpaid',
            'payment_due_date' => null,
        ]);

        // Calculate total fine to pay
        $totalFine = $borrow->late_fee + $request->additional_fine;

        Activity::log(Auth::id(), 'damage_response', "Merespons laporan kerusakan buku {$borrow->book->title} dari {$borrow->user->name}. Denda tambahan: Rp " . number_format($request->additional_fine, 0, ',', '.'));

        return redirect()->route('petugas.borrow-management')
            ->with('success', "Respons laporan kerusakan dikirim ke {$borrow->user->name}. Total denda yang harus dibayar: Rp " . number_format($totalFine, 0, ',', '.'));
    }

    /**
     * Petugas: Tandai masalah kerusakan selesai
     */
    public function markDamageResolved(Borrow $borrow)
    {
        if ($borrow->damage_status !== 'responded') {
            abort(403, 'Hanya laporan yang sudah direspons yang dapat ditandai selesai.');
        }

        $borrow->update([
            'damage_status' => 'resolved',
        ]);

        Activity::log(Auth::id(), 'damage_resolved', "Menandai masalah kerusakan buku {$borrow->book->title} dari {$borrow->user->name} selesai.");

        return back()->with('success', 'Masalah kerusakan buku telah ditandai selesai.');
    }
}
