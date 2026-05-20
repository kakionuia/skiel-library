<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Borrow;
use App\Models\Review;
use App\Models\Complaint;
use App\Models\Suggestion;
use App\Models\BookRequest;
use App\Models\Activity;
use App\Models\SystemParameter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalBooks = Book::count();
        $totalBorrows = Borrow::count();
        $pendingApprovals = Borrow::where('status', 'pending')->count();
        $lateReturns = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();
        
        // Add member and staff stats
        $totalMembers = User::where('role', 'anggota')->count();
        $totalPetugas = User::where('role', 'petugas')->count();
        $pendingMembers = User::where('role', 'anggota')->where('status', 'pending')->count();

        $recentBorrows = Borrow::with('user', 'book')
            ->latest()
            ->limit(10)
            ->get();

        $topBooks = Book::withCount('borrows')
            ->orderByDesc('borrows_count')
            ->limit(6)
            ->get();

        $monthlyBorrows = collect(range(5, 0))
            ->map(function ($monthsAgo) {
                $date = now()->subMonths($monthsAgo);

                return [
                    'month' => $date->translatedFormat('M'),
                    'count' => Borrow::whereYear('borrow_date', $date->year)
                        ->whereMonth('borrow_date', $date->month)
                        ->count(),
                ];
            })
            ->values();

        $totalLateFee = (float) Borrow::whereNotNull('payment_date')
            ->where('payment_status', 'confirmed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('late_fee');

        if ($totalLateFee <= 0) {
            $totalLateFee = (float) Borrow::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('late_fee');
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalBooks' => $totalBooks,
                'totalBorrows' => $totalBorrows,
                'pendingApprovals' => $pendingApprovals,
                'lateReturns' => $lateReturns,
                'totalMembers' => $totalMembers,
                'totalPetugas' => $totalPetugas,
                'pendingMembers' => $pendingMembers,
                'totalLateFee' => $totalLateFee,
                'lateReturnsCount' => $lateReturns,
                'pendingBookRequests' => BookRequest::where('status', 'pending')->count(),
            ],
            'monthlyBorrows' => $monthlyBorrows,
            'recentBorrows' => $recentBorrows,
            'topBooks' => $topBooks,
        ]);
    }

    public function downloadReport()
    {
        $stats = [
            'totalBooks' => Book::count(),
            'totalBorrows' => Borrow::count(),
            'totalMembers' => User::where('role', 'anggota')->count(),
            'totalPetugas' => User::where('role', 'petugas')->count(),
            'pendingMembers' => User::where('role', 'anggota')->where('status', 'pending')->count(),
            'lateReturns' => Borrow::where('status', 'borrowed')
                ->where('due_date', '<', now())
                ->count(),
            'totalFine' => Borrow::where('status', 'borrowed')
                ->where('due_date', '<', now())
                ->sum('late_fee') ?? 0,
        ];

        return view('reports.digital-receipt', ['stats' => $stats, 'generatedAt' => now()]);
    }

    // Accounts Management
    public function accounts(Request $request)
    {
        // Get petugas (staff)
        $petugas = User::where('role', 'petugas')->get();

        // Get ALL anggota (members) - not just pending
        $anggota = User::where('role', 'anggota')->get();

        return Inertia::render('Admin/Accounts/Index', [
            'petugas' => $petugas,
            'anggota' => $anggota,
        ]);
    }

    // Book Management
    public function books(Request $request)
    {
        $books = Book::all();
        $categories = Category::all();

        return Inertia::render('Admin/Books/Index', [
            'books' => $books,
            'categories' => $categories,
        ]);
    }

    public function storeBook(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books',
            'category' => 'required|string',
            'publisher' => 'nullable|string',
            'year' => 'nullable|integer',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $validated['available_quantity'] = $validated['quantity'];
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'approved';

        $book = Book::create($validated);
        Activity::log(Auth::id(), 'create', "Menambahkan buku: {$book->title}");

        return redirect()->route('admin.books')->with('success', 'Buku berhasil ditambahkan');
    }

    public function editBook(Book $book)
    {
        $categories = Category::all();

        return Inertia::render('Admin/Books/Edit', [
            'book' => $book,
            'categories' => $categories,
        ]);
    }

    public function updateBook(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'category' => 'required|string',
            'publisher' => 'nullable|string',
            'year' => 'nullable|integer',
            'quantity' => 'required|integer|min:1',
            'available_quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book->update($validated);
        Activity::log(Auth::id(), 'update', "Mengubah data buku: {$book->title}");

        return redirect()->route('admin.books')->with('success', 'Buku berhasil diperbarui');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);
        Activity::log(Auth::id(), 'create', "Menambahkan kategori: {$validated['name']}");

        return back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function destroyCategory(Request $request, $category)
    {
        $cat = Category::where('name', $category)->first();
        if ($cat) {
            $cat->delete();
            Activity::log(Auth::id(), 'delete', "Menghapus kategori: {$category}");
        }
        return back()->with('success', 'Kategori berhasil dihapus');
    }

    public function destroyBook(Book $book)
    {
        $bookTitle = $book->title;
        $book->delete();
        Activity::log(Auth::id(), 'delete', "Menghapus buku: {$bookTitle}");

        return back()->with('success', 'Buku berhasil dihapus');
    }

    // Book Requests
    public function bookRequests()
    {
        $requests = BookRequest::with('user')->latest()->paginate(10);

        return Inertia::render('Admin/BookRequests/Index', [
            'requests' => $requests,
        ]);
    }

    public function approveBookRequest(BookRequest $bookRequest)
    {
        $bookRequest->update(['status' => 'approved']);
        Activity::log(Auth::id(), 'approve', "Menyetujui permintaan buku: {$bookRequest->title}");

        return back()->with('success', 'Permintaan buku disetujui');
    }

    public function rejectBookRequest(BookRequest $bookRequest)
    {
        $bookRequest->update(['status' => 'rejected']);
        Activity::log(Auth::id(), 'reject', "Menolak permintaan buku: {$bookRequest->title}");

        return back()->with('success', 'Permintaan buku ditolak');
    }

    // Book Submissions from Petugas
    public function bookSubmissions()
    {
        $submissions = Book::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'category' => $book->category,
                    'quantity' => $book->quantity,
                    'description' => $book->description,
                    'status' => $book->status,
                    'created_by_user' => $book->user,
                    'created_at' => $book->created_at,
                ];
            });

        return Inertia::render('Admin/BookSubmissions', [
            'submissions' => $submissions,
        ]);
    }

    public function showBookSubmission(Book $book)
    {
        $bookData = [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'publisher' => $book->publisher,
            'year' => $book->year,
            'isbn' => $book->isbn,
            'category' => $book->category,
            'quantity' => $book->quantity,
            'description' => $book->description,
            'status' => $book->status,
            'rejection_reason' => $book->rejection_reason,
            'created_by_user' => $book->user,
            'created_at' => $book->created_at,
        ];

        return Inertia::render('Admin/BookSubmissionDetail', [
            'submission' => $bookData,
        ]);
    }

    public function approveBookSubmission(Book $book)
    {
        $book->update(['status' => 'approved']);
        Activity::log(Auth::id(), 'approve', "Menyetujui pengajuan buku: {$book->title}");

        return back()->with('success', 'Buku disetujui');
    }

    public function rejectBookSubmission(Book $book)
    {
        $reason = '';
        if (request()->has('rejection_reason')) {
            $reason = request('rejection_reason');
            $book->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);
        } else {
            $book->update(['status' => 'rejected']);
        }
        Activity::log(Auth::id(), 'reject', "Menolak pengajuan buku: {$book->title}" . ($reason ? " (Alasan: {$reason})" : ''));

        return back()->with('success', 'Buku ditolak');
    }

    // Reviews Management
    public function reviews()
    {
        $reviews = Review::with('user', 'book')->latest()->paginate(10);

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews,
        ]);
    }

    public function destroyReview(Review $review)
    {
        $bookTitle = $review->book?->title ?? 'Unknown';
        $review->delete();
        Activity::log(Auth::id(), 'delete', "Menghapus review buku: {$bookTitle}");

        return back()->with('success', 'Review dihapus');
    }

    // Complaints Management
    public function complaints()
    {
        $complaints = Complaint::with('user')->latest()->paginate(10);

        return Inertia::render('Admin/Complaints/Index', [
            'complaints' => $complaints,
        ]);
    }

    public function showComplaint(Complaint $complaint)
    {
        return Inertia::render('Admin/ComplaintDetail', [
            'complaint' => $complaint,
        ]);
    }

    public function resolveComplaint(Complaint $complaint)
    {
        $complaint->update(['status' => 'resolved']);
        Activity::log(Auth::id(), 'update', "Menyelesaikan keluhan dari: {$complaint->user?->name}");

        return back()->with('success', 'Keluhan diselesaikan');
    }

    // Suggestions Management
    public function suggestions()
    {
        $suggestions = Suggestion::latest()->paginate(10);

        return Inertia::render('Admin/Suggestions/Index', [
            'suggestions' => $suggestions,
        ]);
    }

    public function updateSuggestionStatus(Suggestion $suggestion)
    {
        $suggestion->update(['status' => 'processed']);
        Activity::log(Auth::id(), 'update', "Memproses saran dari: {$suggestion->user?->name}");

        return back()->with('success', 'Saran diproses');
    }

    // Late Returns
    public function lateReturns()
{
    $lateReturns = Borrow::whereIn('status', ['approved', 'borrowed'])
        ->where('due_date', '<', now())
        ->with(['user', 'book'])
        ->latest()
        ->paginate(10);
    
    // Calculate dynamic late fees
    $lateReturns->getCollection()->transform(function ($borrow) {
        $borrow->calculateLateFee();
        return $borrow;
    });

    return Inertia::render('Admin/LateReturns/Index', [
        'lateBorrows' => $lateReturns,
    ]);
}

    // Activity Logs
    public function activityLogs(Request $request)
    {
        $role = $request->query('role', 'anggota');
        
        $activities = Activity::with('user')
            ->when($role && in_array($role, ['anggota', 'petugas', 'admin']), function($query) use ($role) {
                return $query->whereHas('user', function($q) use ($role) {
                    $q->where('role', $role);
                });
            })
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/ActivityLogs/Index', [
            'activities' => $activities,
            'currentRole' => $role,
        ]);
    }

    // Other methods
    public function storePetugas(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        $validated['role'] = 'petugas';
        $validated['status'] = 'approved';
        $validated['password'] = bcrypt($validated['password'] ?? 'password123');

        $user = User::create($validated);
        Activity::log(Auth::id(), 'create', "Menambahkan petugas: {$user->name}");

        return back()->with('success', 'Petugas berhasil ditambahkan');
    }

    public function approveUser(User $user)
    {
        $user->update(['status' => 'approved']);
        Activity::log(Auth::id(), 'approve', "Menyetujui anggota: {$user->name}");

        return back()->with('success', 'Anggota berhasil diverifikasi');
    }

    public function rejectUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $user->update([
            'status' => 'rejected',
        ]);
        Activity::log(Auth::id(), 'reject', "Menolak anggota: {$user->name}");

        return back()->with('success', 'Anggota ditolak');
    }

    public function showUser(User $user)
    {
        return Inertia::render('Admin/Accounts/Show', [
            'user' => $user,
        ]);
    }

    public function showPetugas(User $petugas)
    {
        $activities = Activity::where('user_id', $petugas->id)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return Inertia::render('Admin/Accounts/ShowPetugas', [
            'petugas' => $petugas,
            'activities' => $activities,
        ]);
    }

    public function restrictUser(User $user)
    {
        $user->update(['borrow_restricted' => true]);
        Activity::log(Auth::id(), 'restrict', "Membatasi peminjaman anggota: {$user->name}");

        return back()->with('success', 'Anggota dibatasi peminjaman');
    }

    public function unrestrictUser(User $user)
    {
        $user->update(['borrow_restricted' => false]);
        Activity::log(Auth::id(), 'unrestrict', "Menghapus batasan peminjaman anggota: {$user->name}");

        return back()->with('success', 'Pembatasan anggota dihapus');
    }

    public function destroyUser(User $user)
    {
        $userName = $user->name;
        $user->delete();
        Activity::log(Auth::id(), 'delete', "Menghapus anggota: {$userName}");

        return back()->with('success', 'Anggota dihapus');
    }
    public function createBook()
    {
        $categories = Category::all();
        return Inertia::render('Admin/Books/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Admin: Halaman pengaturan parameter sistem
     */
    public function systemParameters()
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

        return Inertia::render('Admin/Settings/SystemParameters', [
            'parameters' => $formattedParams,
        ]);
    }

    /**
     * Admin: Update parameter sistem
     */
    public function updateSystemParameter(Request $request, SystemParameter $parameter)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'value' => 'required',
        ]);

        $oldValue = $parameter->value;
        $parameter->update(['value' => (string) $request->value]);

        Activity::log(Auth::id(), 'update_parameter', "Mengubah parameter '{$parameter->key}' dari '{$oldValue}' menjadi '{$parameter->value}'");

        return back()->with('success', "Parameter '{$parameter->key}' berhasil diperbarui.");
    }

    /**
     * Admin: Daftar permintaan perpanjangan peminjaman (delegation ke BorrowController)
     */
    public function extensionRequests()
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'petugas'])) {
            abort(403, 'Unauthorized');
        }

        $borrowController = new \App\Http\Controllers\BorrowController();
        return $borrowController->extensionRequests(request());
    }

    /**
     * Admin: Daftar pembayaran denda yang pending
     */
public function pendingPayments()
{
    $user = Auth::user();
    
    if (!in_array($user->role, ['admin', 'petugas'])) {
        abort(403, 'Unauthorized');
    }

    Borrow::whereIn('status', ['approved', 'borrowed'])
        ->where('due_date', '<', now())
        ->get()
        ->each
        ->calculateLateFee();

    $borrows = Borrow::where('payment_status', 'pending')
        ->whereIn('payment_method', ['qris', 'cash'])
        ->where('late_fee', '>', 0)
        ->with(['user', 'book'])
        ->orderBy('updated_at', 'desc')
        ->paginate(20);

    return Inertia::render('Admin/Payments/PendingPayments', [
        'borrows' => $borrows,
    ]);
}
    /**
     * Admin: Konfirmasi pembayaran tunai
     */
    public function confirmCashPayment(Borrow $borrow)
    {
        $borrowController = new \App\Http\Controllers\BorrowController();
        return $borrowController->confirmCashPayment($borrow);
    }

    public function confirmQrisPayment(Borrow $borrow)
    {
        $borrowController = new \App\Http\Controllers\BorrowController();
        return $borrowController->confirmQrisPayment($borrow);
    }

    public function addAdditionalFine(Request $request, Borrow $borrow)
    {
        return app(\App\Http\Controllers\Petugas\PetugasController::class)->addAdditionalFine($request, $borrow);
    }

    public function resolveLateReturn(Borrow $borrow)
    {
        return app(\App\Http\Controllers\Petugas\PetugasController::class)->resolveLateReturn($borrow);
    }

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
                'due_date' => $borrow->due_date,
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
                }, json_decode($borrow->damage_images, true) ?? []) : [],
                'additional_fine' => (float) ($borrow->additional_fine ?? 0),
                'staff_comment' => $borrow->staff_comment,
            ];
        });
    
    // Update lateReturns query untuk admin index
    $lateReturns = Borrow::whereIn('status', ['approved', 'borrowed'])
        ->where('due_date', '<', now())
        ->with('user', 'book')
        ->latest()
        ->paginate(10);
    
    return Inertia::render('Admin/LateReturns/Show', [
        'user' => $user,
        'lateBorrows' => $lateBorrows,
    ]);
}

/**
 * Kirim notifikasi keterlambatan ke user spesifik
 */
public function sendLateReminder(User $user)
{
    // TODO: Implement notification system (email, WhatsApp, etc.)
    // Untuk saat ini, log activity saja
    Activity::log(Auth::id(), 'send_reminder', "Mengirim notifikasi keterlambatan ke: {$user->name}");
    
    return back()->with('success', "Notifikasi keterlambatan dikirim ke {$user->name}.");
}

/**
 * Kirim notifikasi massal ke semua anggota terlambat
 */
public function sendBulkLateReminder()
{
    $lateUsers = Borrow::whereIn('status', ['approved', 'borrowed'])
        ->where('due_date', '<', now())
        ->distinct('user_id')
        ->pluck('user_id');
    
    foreach ($lateUsers as $userId) {
        // TODO: Implement notification system
        Activity::log(Auth::id(), 'bulk_reminder', "Notifikasi massal keterlambatan terkirim");
    }
    
    return back()->with('success', "Notifikasi massal dikirim ke {$lateUsers->count()} anggota.");
}
}
