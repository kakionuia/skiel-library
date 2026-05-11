<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\BookRequest;
use App\Models\Activity;
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
                    'status' => 'available', // For display purposes
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

        // Merge for display
        $books = $approvedBooks->merge($pendingBooks);

        return inertia('Petugas/Books/Index', [
            'books' => $books,
        ]);
    }

    public function createBook()
    {
        return inertia('Petugas/Books/Create');
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
            'available_quantity' => $book->available_quantity + $qtyDiff,
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

    // Add Book Request
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
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function($borrow) {
                $daysOverdue = now()->diffInDays($borrow->due_date);
                return [
                    'id' => $borrow->id,
                    'member_name' => $borrow->user?->name ?? 'Unknown',
                    'member_id' => $borrow->user?->id ?? 'N/A',
                    'book_title' => $borrow->book?->title ?? 'Unknown',
                    'borrow_date' => $borrow->borrow_date,
                    'due_date' => $borrow->due_date,
                    'days_overdue' => $daysOverdue,
                    'fine' => ($daysOverdue * 5000), // Rp 5000 per hari
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

    // Activity Logs
    public function activityLogs()
    {
        $activities = Activity::orderBy('created_at', 'desc')
            ->get()
            ->map(function($activity) {
                $typeMap = ['create' => 'create', 'update' => 'update', 'delete' => 'delete', 'request_book' => 'request'];
                return [
                    'id' => $activity->id,
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

            return [
                'id' => $borrow->id,
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
                'late_days' => $borrow->late_days ?? 0,
                'late_fee' => $borrow->late_fee ?? 0,
                'notes' => $borrow->notes,
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

// Update processReturn method dengan perhitungan denda:
public function processReturn(Request $request, Borrow $borrow)
{
    $request->validate([
        'return_date' => 'required|date',
    ]);

    $returnDate = \Carbon\Carbon::parse($request->return_date);
    $lateDays = 0;
    $lateFee = 0;

    // Hitung keterlambatan
    if ($returnDate->gt($borrow->due_date)) {
        $lateDays = $returnDate->diffInDays($borrow->due_date);
        $lateFee = $lateDays * 1000; // Rp1000 per hari (bisa disesuaikan)
    }

    $borrow->update([
        'return_date' => $returnDate,
        'status' => 'returned',
        'late_days' => $lateDays,
        'late_fee' => $lateFee,
    ]);

    // Kembalikan stok buku
    $book = $borrow->book;
    if ($book) {
        $book->increment('available_quantity', $borrow->quantity ?? 1);
    }

    Activity::log(Auth::id(), 'return', "Memproses pengembalian buku: {$book?->title}");

    $message = 'Buku berhasil dikembalikan.';
    if ($lateDays > 0) {
        $message .= " Terlambat {$lateDays} hari. Denda: Rp " . number_format($lateFee, 0, ',', '.');
    }

    return back()->with('success', $message);
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
            return $daysOverdue * 5000;
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
        return inertia('Petugas/Settings', [
            'user' => Auth::user(),
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

        // Store settings di cache atau file config
        cache()->put('library.max_borrow_days', $request->max_borrow_days, now()->addYear());
        cache()->put('library.late_fee_per_day', $request->late_fee_per_day, now()->addYear());
        cache()->put('library.max_books_per_member', $request->max_books_per_member, now()->addYear());

        Activity::log(Auth::id(), 'update', 'Memperbarui parameter sistem perpustakaan');

        return back()->with('success', 'Parameter sistem berhasil diperbarui.');
    }
}
