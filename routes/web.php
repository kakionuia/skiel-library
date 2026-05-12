<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Petugas\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaitingController;
use App\Http\Controllers\Auth\StatusCheckController;
use App\Http\Controllers\Auth\AccountDeletionController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\BookQueueController;
use App\Http\Controllers\ArticleController;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Complaint;
use App\Models\Review;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

// API endpoint for articles (public)
Route::get('/api/articles', function () {
    $articles = \App\Models\Article::where('status', 'approved')
        ->with('author')
        ->latest('published_at')
        ->limit(10)
        ->get()
        ->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'content' => $article->content,
                'category' => $article->category,
                'featured_image' => $article->featured_image,
                'author' => $article->author->name ?? 'Admin',
                'published_at' => $article->published_at?->format('Y-m-d'),
                'desc' => $article->excerpt,
            ];
        });

    return response()->json(['data' => $articles]);
});

// Public articles listing
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');

// Public article detail by slug
Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/download-report', [AdminController::class, 'downloadReport'])->name('download-report');
    
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
    
    // Book Management (Admin can add and manage books + categories)
    Route::get('/books', [AdminController::class, 'books'])->name('books');
    Route::get('/books/create', [AdminController::class, 'createBook'])->name('books.create');
    Route::post('/books', [AdminController::class, 'storeBook'])->name('books.store');
    Route::get('/books/{book}/edit', [AdminController::class, 'editBook'])->name('books.edit');
    Route::put('/books/{book}', [AdminController::class, 'updateBook'])->name('books.update');
    Route::delete('/books/{book}', [AdminController::class, 'destroyBook'])->name('books.destroy');
    
    // Category Management (Admin can add and delete categories)
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
    
    // Admin Borrow Management
    Route::post('/borrows/{borrow}/approve', [BorrowController::class, 'approve'])->name('borrows.approve');
    Route::post('/borrows/{borrow}/reject', [BorrowController::class, 'reject'])->name('borrows.reject');
    Route::post('/borrows/{borrow}/confirm-pickup', [BorrowController::class, 'confirmPickup'])->name('borrows.confirm-pickup');
    Route::post('/borrows/{borrow}/return', [BorrowController::class, 'returnBook'])->name('borrows.return');
    
    // Article Management
    Route::get('/articles', [ArticleController::class, 'admin'])->name('articles');
    Route::get('/articles/create', [ArticleController::class, 'adminCreate'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'adminStore'])->name('articles.store');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'adminEdit'])->name('articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'adminUpdate'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'adminDestroy'])->name('articles.destroy');
    Route::post('/articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
    Route::post('/articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');
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
    
    // Book Submissions
    Route::get('/submissions', [PetugasController::class, 'mySubmissions'])->name('submissions');
    
    // Book Requests
    Route::get('/book-requests', [PetugasController::class, 'bookRequests'])->name('book-requests');
    Route::post('/book-requests', [PetugasController::class, 'storeBookRequest'])->name('book-requests.store');
    
    // Borrows Management
    Route::get('/borrows', [PetugasController::class, 'borrows'])->name('borrows');
    Route::post('/borrows/{borrow}/return', [PetugasController::class, 'processReturn'])->name('borrows.return');
    
    // Borrow Approve/Reject untuk Petugas
    Route::post('/borrows/{borrow}/approve', [BorrowController::class, 'approve'])->name('borrows.approve');
    Route::post('/borrows/{borrow}/reject', [BorrowController::class, 'reject'])->name('borrows.reject');
    Route::post('/borrows/{borrow}/confirm-pickup', [BorrowController::class, 'confirmPickup'])->name('borrows.confirm-pickup');
    
    // Late Returns
    Route::get('/late-returns', [PetugasController::class, 'lateReturns'])->name('late-returns');
    
    // Reports
    Route::get('/reports', [PetugasController::class, 'reports'])->name('reports');
    
    // Activity Logs
    Route::get('/activity-logs', [PetugasController::class, 'activityLogs'])->name('activity-logs');
    
    // Settings
    Route::get('/settings', [PetugasController::class, 'settings'])->name('settings');
    Route::post('/settings', [PetugasController::class, 'updateSettings'])->name('settings.update');
    
    // Article Management
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/my', [ArticleController::class, 'myArticles'])->name('articles.my');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
});

// ========== USER ROUTES (approved users only) ==========
Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();

        $activeBorrows = Borrow::with('book')
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'borrowed'])
            ->orderBy('due_date', 'asc')
            ->get();

        $recentBorrows = Borrow::with('book')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($borrow) => [
                'id' => $borrow->id,
                'status' => $borrow->status,
                'status_text' => ucfirst($borrow->status),
                'due_date' => $borrow->due_date,
                'return_date' => $borrow->return_date,
                'book' => [
                    'id' => $borrow->book?->id,
                    'title' => $borrow->book?->title,
                    'author' => $borrow->book?->author,
                    'publisher' => $borrow->book?->publisher,
                    'cover_url' => $borrow->book?->cover_image ? asset('storage/' . $borrow->book->cover_image) : null,
                ],
            ]);

        $overdueCount = Borrow::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_date', '<', now())
            ->count();

        $totalFines = Borrow::where('user_id', $user->id)
            ->where('status', '!=', 'returned')
            ->where('due_date', '<', now())
            ->sum('late_fee') ?? 0;

        $totalBorrows = Borrow::where('user_id', $user->id)->count();

        $availableBooks = Book::query()
            ->where('status', 'approved')
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews', 'borrows'])
            // Order by available quantity first (tersedia di atas), then by popularity (borrows count)
            ->orderByRaw('CASE WHEN available_quantity > 0 THEN 0 ELSE 1 END ASC')
            ->orderByDesc('borrows_count')
            ->orderBy('title')
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
            'activeLoans' => Borrow::whereIn('status', ['approved', 'borrowed'])->count(),
            'todayLoans' => Borrow::whereDate('borrow_date', now()->toDateString())->count(),
            'todayReturns' => Borrow::whereDate('return_date', now()->toDateString())->count(),
            'libraryOverdue' => Borrow::whereIn('status', ['approved', 'borrowed'])
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
            'finePerDay' => 1000,
            'availableBooks' => $availableBooks,
            'popularBooks' => $popularBooks,
            'latestReviews' => $latestReviews,
            'userReviews' => $userReviews,
            'userComplaints' => $userComplaints,
            'userSuggestions' => $userSuggestions,
            'dashboardStats' => $dashboardStats,
        ]);
    })->name('dashboard');

    // ========== BOOK DETAIL ROUTE ==========
    Route::get('/books/{book}', function (Book $book) {
        // Hanya buku yang approved yang bisa diakses
        if ($book->status !== 'approved') {
            abort(404);
        }

        $book->loadAvg('reviews', 'rating')->loadCount(['reviews', 'borrows']);

        $activeBorrow = Borrow::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->latest()
            ->first();
        
        $bookData = [
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
            'reviews_count' => $book->reviews_count ?? 0,
            'borrows_count' => $book->borrows_count ?? 0,
        ];
        
        // Get reviews
        $reviews = Review::with('user:id,name,kelas,jurusan')
            ->where('book_id', $book->id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user?->name ?? 'Anonymous',
                    'user_class' => trim(($review->user?->kelas ?? '') . ' ' . ($review->user?->jurusan ?? '')),
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                ];
            });

        $otherBooks = Book::query()
            ->where('status', 'approved')
            ->where('id', '!=', $book->id)
            ->inRandomOrder()
            ->limit(4)
            ->get()
            ->map(fn ($otherBook) => [
                'id' => $otherBook->id,
                'title' => $otherBook->title,
                'author' => $otherBook->author,
                'category' => $otherBook->category ?: 'Umum',
                'available_quantity' => (int) $otherBook->available_quantity,
                'cover_image' => $otherBook->cover_image ? asset('storage/' . $otherBook->cover_image) : null,
            ]);
        
        return Inertia::render('User/BookDetail', [
            'book' => $bookData,
            'reviews' => $reviews,
            'otherBooks' => $otherBooks,
            'activeBorrow' => $activeBorrow ? [
                'id' => $activeBorrow->id,
                'status' => $activeBorrow->status,
                'status_text' => ucfirst($activeBorrow->status),
            ] : null,
        ]);
    })->name('books.show');

    Route::get('/books/{book}/review', function (Book $book) {
        if ($book->status !== 'approved') {
            abort(404);
        }

        $hasReturnedBorrow = Borrow::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->where('status', 'returned')
            ->exists();

        abort_unless($hasReturnedBorrow, 403);

        $existingReview = Review::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->first();

        return Inertia::render('User/ReviewBook', [
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'category' => $book->category ?: 'Umum',
                'cover_image' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
            ],
            'existingReview' => $existingReview ? [
                'rating' => $existingReview->rating,
                'comment' => $existingReview->comment,
            ] : null,
        ]);
    })->name('books.review.create');

    // ========== BORROW ROUTES ==========
    Route::post('/borrow', [BorrowController::class, 'store'])->name('borrow.store');
    Route::get('/borrow/create/{book}', function (Book $book) {
        if ($book->status !== 'approved') {
            abort(404);
        }
        return Inertia::render('User/BorrowBook', [
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'year' => $book->year,
                'isbn' => $book->isbn,
                'category' => $book->category ?: 'Umum',
                'available_quantity' => (int) $book->available_quantity,
                'cover_image' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
            ]
        ]);
    })->name('borrow.create');
    Route::get('/my-loans', [BorrowController::class, 'myLoans'])->name('borrow.my-loans');
    Route::get('/my-queue', [BookQueueController::class, 'myQueue'])->name('queue.my-queue');
    Route::get('/borrow/{borrow}', [BorrowController::class, 'show'])->name('borrow.show');
    Route::post('/borrow/{borrow}/return', [BorrowController::class, 'returnBook'])->name('borrow.return');
    Route::get('/borrow/{borrow}/print', [BorrowController::class, 'printProof'])->name('borrow.print');
    Route::post('/borrow/{borrow}/extend', [BorrowController::class, 'extend'])->name('borrow.extend');
    Route::delete('/borrow/{borrow}', [BorrowController::class, 'cancel'])->name('borrow.cancel');

    // ========== BOOK QUEUE ROUTES ==========
    Route::post('/queue/add', [BookQueueController::class, 'store'])->name('queue.store');
    Route::delete('/queue/{queue}', [BookQueueController::class, 'cancel'])->name('queue.cancel');
    Route::get('/api/queue/position', [BookQueueController::class, 'getPosition'])->name('queue.position');
    Route::get('/api/queue/book/{book}', [BookQueueController::class, 'bookQueueInfo'])->name('queue.info');

    // ========== COMPLAINTS ==========
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

    // ========== SUGGESTIONS ==========
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

    // ========== REVIEWS ==========
    Route::post('/dashboard/reviews', function (Request $request) {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1500',
        ]);

        $book = Book::where('status', 'approved')->findOrFail($validated['book_id']);

        $hasReturnedBorrow = Borrow::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->where('status', 'returned')
            ->exists();

        abort_unless($hasReturnedBorrow, 403);

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

// ========== PROFILE ROUTES ==========
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ========== ARTICLE ROUTES (PUBLIC) ==========
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

// ========== DEBUG ROUTE ==========
Route::get('/debug/books', function() {
    $books = Book::where('status', 'approved')
        ->select('id', 'title', 'available_quantity', 'quantity', 'status', 'category')
        ->orderBy('available_quantity', 'DESC')
        ->limit(100)
        ->get();
    
    return response()->json([
        'total' => Book::where('status', 'approved')->count(),
        'out_of_stock' => Book::where('status', 'approved')->where('available_quantity', 0)->count(),
        'books' => $books
    ]);
})->name('debug.books');

Route::get('/debug/book/{id}', function($id) {
    $book = Book::find($id);
    if (!$book) {
        return response()->json(['error' => 'Book not found'], 404);
    }
    
    return response()->json([
        'id' => $book->id,
        'title' => $book->title,
        'quantity' => $book->quantity,
        'available_quantity' => $book->available_quantity,
        'status' => $book->status,
        'category' => $book->category,
        'created_at' => $book->created_at,
        'borrows' => $book->borrows()->select('id', 'status')->get()
    ]);
})->name('debug.book.detail');

require __DIR__.'/auth.php';
