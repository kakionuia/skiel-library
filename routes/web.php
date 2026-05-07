<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Petugas\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaitingController;
use App\Http\Controllers\Auth\StatusCheckController;
use App\Http\Controllers\Auth\AccountDeletionController;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Complaint;
use App\Models\Review;
use App\Models\Suggestion;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\ContentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

// Waiting page for unapproved accounts
Route::middleware(['auth', 'pending'])->get('/waiting', [WaitingController::class, 'index'])->name('waiting');

// Delete account for pending/rejected users
Route::middleware(['auth', 'pending'])->post('/delete-account', [AccountDeletionController::class, 'delete'])->name('account.delete');

// API endpoint to check approval status
Route::middleware('auth')->get('/api/check-approval-status', [StatusCheckController::class, 'checkApprovalStatus']);

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Accounts
    Route::get('/accounts', [AdminController::class, 'accounts'])->name('accounts');
    Route::post('/accounts/petugas', [AdminController::class, 'storePetugas'])->name('accounts.petugas.store');
    Route::post('/accounts/{user}/approve', [AdminController::class, 'approveUser'])->name('accounts.approve');
    Route::post('/accounts/{user}/reject', [AdminController::class, 'rejectUser'])->name('accounts.reject');
    Route::get('/accounts/anggota/{user}', [AdminController::class, 'showUser'])->name('accounts.anggota.show');
    Route::get('/accounts/petugas/{petugas}', [AdminController::class, 'showPetugas'])->name('accounts.petugas.show');
    Route::post('/accounts/{user}/restrict', [AdminController::class, 'restrictUser'])->name('accounts.restrict');
    Route::post('/accounts/{user}/unrestrict', [AdminController::class, 'unrestrictUser'])->name('accounts.unrestrict');
    Route::delete('/accounts/{user}', [AdminController::class, 'destroyUser'])->name('accounts.destroy');
    
    // Book Requests
    Route::get('/book-requests', [AdminController::class, 'bookRequests'])->name('book-requests');
    Route::post('/book-requests/{bookRequest}/approve', [AdminController::class, 'approveBookRequest'])->name('book-requests.approve');
    Route::post('/book-requests/{bookRequest}/reject', [AdminController::class, 'rejectBookRequest'])->name('book-requests.reject');
    
    // Book Submissions from Petugas
    Route::get('/book-submissions', [AdminController::class, 'bookSubmissions'])->name('book-submissions');
    Route::get('/book-submissions/{book}', [AdminController::class, 'showBookSubmission'])->name('book-submissions.show');
    Route::post('/book-submissions/{book}/approve', [AdminController::class, 'approveBookSubmission'])->name('book-submissions.approve');
    Route::post('/book-submissions/{book}/reject', [AdminController::class, 'rejectBookSubmission'])->name('book-submissions.reject');
    
    // Late Returns
    Route::get('/late-returns', [AdminController::class, 'lateReturns'])->name('late-returns');
    Route::get('/late-returns/{user}', [AdminController::class, 'showLateReturn'])->name('late-returns.show');
    
    // Reviews
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::delete('/reviews/{review}', [AdminController::class, 'destroyReview'])->name('reviews.destroy');
    
    // Complaints
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints');
    Route::get('/complaints/{complaint}', [AdminController::class, 'showComplaint'])->name('complaints.show');
    Route::post('/complaints/{complaint}/resolve', [AdminController::class, 'resolveComplaint'])->name('complaints.resolve');
    
    // Suggestions
    Route::get('/suggestions', [AdminController::class, 'suggestions'])->name('suggestions');
    Route::post('/suggestions/{suggestion}/update', [AdminController::class, 'updateSuggestionStatus'])->name('suggestions.update');
    
    // Activity Logs
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
});

// Petugas Routes
Route::middleware(['auth', 'verified', 'petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
    
    // Books Management
    Route::get('/books', [PetugasController::class, 'books'])->name('books');
    Route::get('/books/create', [PetugasController::class, 'createBook'])->name('books.create');
    Route::post('/books', [PetugasController::class, 'storeBook'])->name('books.store');
    Route::get('/books/{book}/edit', [PetugasController::class, 'editBook'])->name('books.edit');
    Route::put('/books/{book}', [PetugasController::class, 'updateBook'])->name('books.update');
    Route::delete('/books/{book}', [PetugasController::class, 'destroyBook'])->name('books.destroy');
    
    // Book Submissions - Pengajuan Buku
    Route::get('/submissions', [PetugasController::class, 'mySubmissions'])->name('submissions');
    
    // Book Requests
    Route::get('/book-requests', [PetugasController::class, 'bookRequests'])->name('book-requests');
    Route::post('/book-requests', [PetugasController::class, 'storeBookRequest'])->name('book-requests.store');
    
    // Borrows - Kelola Peminjaman
    Route::get('/borrows', [PetugasController::class, 'borrows'])->name('borrows');
    Route::post('/borrows/{borrow}/return', [PetugasController::class, 'processReturn'])->name('borrows.return');
    
    // Late Returns
    Route::get('/late-returns', [PetugasController::class, 'lateReturns'])->name('late-returns');
    
    // Reports - Laporan Harian
    Route::get('/reports', [PetugasController::class, 'reports'])->name('reports');
    
    // Activity Logs
    Route::get('/activity-logs', [PetugasController::class, 'activityLogs'])->name('activity-logs');
    
    // Settings
    Route::get('/settings', [PetugasController::class, 'settings'])->name('settings');
    Route::post('/settings', [PetugasController::class, 'updateSettings'])->name('settings.update');
});

// User Routes (must be approved)
Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        $activeBorrows = Borrow::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->orderBy('due_date', 'asc')
            ->get();

        $recentBorrows = Borrow::with('book')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $overdueCount = Borrow::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();

        $totalFines = Borrow::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->sum('late_fee') ?? 0;

        $totalBorrows = Borrow::where('user_id', $user->id)->count();

        $availableBooks = Book::query()
            ->where('status', 'approved')
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews', 'borrows'])
            ->orderByDesc('available_quantity')
            ->orderBy('title')
            ->limit(12)
            ->get()
            ->map(fn ($book) => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'year' => $book->year,
                'isbn' => $book->isbn,
                'category' => $book->category ?: 'Umum',
                'quantity' => (int) $book->quantity,
                'available_quantity' => (int) $book->available_quantity,
                'description' => $book->description,
                'cover_image' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
                'average_rating' => round((float) ($book->reviews_avg_rating ?? 0), 1),
                'reviews_count' => $book->reviews_count,
                'borrows_count' => $book->borrows_count,
            ]);

        $popularBooks = Book::query()
            ->where('status', 'approved')
            ->withCount('borrows')
            ->orderByDesc('borrows_count')
            ->orderBy('title')
            ->limit(5)
            ->get(['id', 'title', 'author', 'category', 'available_quantity']);

        $latestReviews = Review::with(['user:id,name', 'book:id,title'])
            ->latest()
            ->limit(5)
            ->get();

        $userReviews = Review::with('book:id,title')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(4)
            ->get();

        $userComplaints = Complaint::where('user_id', $user->id)
            ->latest()
            ->limit(4)
            ->get();

        $userSuggestions = Suggestion::where('user_id', $user->id)
            ->latest()
            ->limit(4)
            ->get();

        $dashboardStats = [
            'totalTitles' => Book::where('status', 'approved')->count(),
            'availableCopies' => (int) Book::where('status', 'approved')->sum('available_quantity'),
            'categories' => Book::where('status', 'approved')
                ->whereNotNull('category')
                ->distinct('category')
                ->count('category'),
            'activeLoans' => Borrow::where('status', 'borrowed')->count(),
            'todayLoans' => Borrow::whereDate('borrow_date', now()->toDateString())->count(),
            'todayReturns' => Borrow::whereDate('return_date', now()->toDateString())->count(),
            'libraryOverdue' => Borrow::where('status', 'borrowed')
                ->where('due_date', '<', now())
                ->count(),
        ];

        return Inertia::render('Dashboard', [
            'user' => $user,
            'activeBorrows' => $activeBorrows,
            'recentBorrows' => $recentBorrows,
            'overdueCount' => $overdueCount,
            'totalBorrows' => $totalBorrows,
            'totalFines' => (int)$totalFines,
            'maxBooksPerUser' => 3,
            'maxLoanDays' => 7,
            'finePerDay' => 5000,
            'availableBooks' => $availableBooks,
            'popularBooks' => $popularBooks,
            'latestReviews' => $latestReviews,
            'userReviews' => $userReviews,
            'userComplaints' => $userComplaints,
            'userSuggestions' => $userSuggestions,
            'dashboardStats' => $dashboardStats,
        ]);
    })->name('dashboard');

    Route::post('/dashboard/complaints', function (Request $request) {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
        ]);

        Complaint::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Keluhan berhasil dikirim.');
    })->name('dashboard.complaints.store');

    Route::post('/dashboard/suggestions', function (Request $request) {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        Suggestion::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Saran berhasil dikirim.');
    })->name('dashboard.suggestions.store');

    Route::post('/dashboard/reviews', function (Request $request) {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1500',
        ]);

        $book = Book::where('status', 'approved')->findOrFail($validated['book_id']);

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return back()->with('success', 'Ulasan buku berhasil disimpan.');
    })->name('dashboard.reviews.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/api/contents', [ContentController::class, 'index']);