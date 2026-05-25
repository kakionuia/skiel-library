<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Petugas\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaitingController;
use App\Http\Controllers\Auth\StatusCheckController;
use App\Http\Controllers\Auth\AccountDeletionController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\BookQueueController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\LiteracyEventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ScanBorrowController;
use App\Http\Controllers\FeedbackController;
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

/*
|--------------------------------------------------------------------------
| WELCOME / HOME ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/', [ArticleController::class, 'welcome'])->name('home');

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
                'featured_image' => $article->featured_image ? asset('storage/' . $article->featured_image) : null,
                'author' => $article->author->name ?? 'Admin',
                'published_at' => $article->published_at?->format('Y-m-d'),
                'desc' => $article->excerpt,
            ];
        });

    return response()->json(['data' => $articles]);
});

/*
|--------------------------------------------------------------------------
| PUBLIC ARTICLE ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/events/{literacyEvent:slug}', [LiteracyEventController::class, 'show'])->name('events.show');
Route::middleware('auth')->group(function () {
    Route::post('/articles/{article}/comments', [ArticleController::class, 'storeComment'])->name('articles.comments.store');
    Route::delete('/article-comments/{comment}', [ArticleController::class, 'deleteComment'])->name('articles.comments.delete');
});
Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::post('/events/{literacyEvent:slug}/participate', [LiteracyEventController::class, 'participate'])->name('events.participate');
});

/*
|--------------------------------------------------------------------------
| FEEDBACK ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/feedback', [FeedbackController::class, 'show'])->name('feedback.show');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/api/books/{bookId}/borrow-history', [BorrowController::class, 'getBorrowHistory'])->name('books.borrow-history');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
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
    
    // Late Return Actions (Admin)
    Route::post('/late-returns/{borrow}/additional-fine', [AdminController::class, 'addAdditionalFine'])->name('late-returns.additional-fine');
    Route::post('/late-returns/{borrow}/resolve', [AdminController::class, 'resolveLateReturn'])->name('late-returns.resolve');
    
    // Notifications
    Route::post('/notifications/send-late-reminder/{user}', [AdminController::class, 'sendLateReminder'])->name('notifications.send-late');
    Route::post('/notifications/send-bulk-late-reminder', [AdminController::class, 'sendBulkLateReminder'])->name('notifications.bulk-late');
    
    // Reviews
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::delete('/reviews/{review}', [AdminController::class, 'destroyReview'])->name('reviews.destroy');
    
    // Complaints & Suggestions
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints');
    Route::get('/suggestions', [AdminController::class, 'complaints'])->name('suggestions'); // Same as complaints (unified)
    Route::get('/complaints/{complaint}', [AdminController::class, 'showComplaint'])->name('complaints.show');
    Route::post('/complaints/{complaint}/resolve', [AdminController::class, 'resolveComplaint'])->name('complaints.resolve');
    Route::post('/suggestions/{suggestion}/update', [AdminController::class, 'updateSuggestionStatus'])->name('suggestions.update');
    
    // Activity Logs
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
    
    // Book Management
    Route::get('/books', [AdminController::class, 'books'])->name('books');
    Route::get('/books/create', [AdminController::class, 'createBook'])->name('books.create');
    Route::post('/books', [AdminController::class, 'storeBook'])->name('books.store');
    Route::get('/books/{book}/edit', [AdminController::class, 'editBook'])->name('books.edit');
    Route::put('/books/{book}', [AdminController::class, 'updateBook'])->name('books.update');
    Route::delete('/books/{book}', [AdminController::class, 'destroyBook'])->name('books.destroy');
    
    // Category Management
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
    
    // Admin Borrow Management
    Route::post('/borrows/{borrow}/approve', [BorrowController::class, 'approve'])->name('borrows.approve');
    Route::post('/borrows/{borrow}/reject', [BorrowController::class, 'reject'])->name('borrows.reject');
    Route::post('/borrows/{borrow}/confirm-pickup', [BorrowController::class, 'confirmPickup'])->name('borrows.confirm-pickup');
    Route::post('/borrows/{borrow}/return', [BorrowController::class, 'returnBook'])->name('borrows.return');
    
    // Borrow Extension Approval/Rejection
    Route::post('/borrow-extensions/{extension}/approve', [BorrowController::class, 'approveExtension'])->name('extensions.approve');
    Route::post('/borrow-extensions/{extension}/reject', [BorrowController::class, 'rejectExtension'])->name('extensions.reject');
    Route::get('/borrow-extensions', [AdminController::class, 'extensionRequests'])->name('extensions.index');
    
    // Payment Management
    Route::get('/payments/pending', [AdminController::class, 'pendingPayments'])->name('payments.pending');
    Route::post('/payments/{borrow}/confirm-cash', [AdminController::class, 'confirmCashPayment'])->name('payments.confirm-cash');
    Route::post('/payments/{borrow}/confirm-qris', [AdminController::class, 'confirmQrisPayment'])->name('payments.confirm-qris');
    
    // Article Management
    Route::get('/articles', [ArticleController::class, 'admin'])->name('articles');
    Route::get('/articles/create', [ArticleController::class, 'adminCreate'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'adminStore'])->name('articles.store');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'adminEdit'])->name('articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'adminUpdate'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'adminDestroy'])->name('articles.destroy');
    Route::post('/articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
    Route::post('/articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');

    // Literacy Event Management
    Route::get('/events', [LiteracyEventController::class, 'adminIndex'])->name('events.index');
    Route::post('/events', [LiteracyEventController::class, 'adminStore'])->name('events.store');
    Route::get('/events/{literacyEvent}', [LiteracyEventController::class, 'adminShow'])->name('events.show');
    Route::put('/events/{literacyEvent}', [LiteracyEventController::class, 'adminUpdate'])->name('events.update');
    Route::delete('/events/{literacyEvent}', [LiteracyEventController::class, 'adminDestroy'])->name('events.destroy');
    Route::post('/events/{literacyEvent}/winner/{user}', [LiteracyEventController::class, 'chooseWinner'])->name('events.winner');
    Route::get('/events/{literacyEvent}/submissions/{participant}', [LiteracyEventController::class, 'viewSubmission'])->name('events.submissions.show');
    
    // System Parameters
    Route::get('/settings/system-parameters', [AdminController::class, 'systemParameters'])->name('settings.system-parameters');
    Route::put('/settings/system-parameters/{parameter}', [AdminController::class, 'updateSystemParameter'])->name('settings.system-parameters.update');
});

/*
|--------------------------------------------------------------------------
| PETUGAS ROUTES
|--------------------------------------------------------------------------
*/
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
    
    // Borrow Approve/Reject
    Route::post('/borrows/{borrow}/approve', [BorrowController::class, 'approve'])->name('borrows.approve');
    Route::post('/borrows/{borrow}/reject', [BorrowController::class, 'reject'])->name('borrows.reject');
    Route::post('/borrows/{borrow}/confirm-pickup', [BorrowController::class, 'confirmPickup'])->name('borrows.confirm-pickup');
    
    // Borrow Extension
    Route::post('/borrow-extensions/{extension}/approve', [BorrowController::class, 'approveExtension'])->name('extensions.approve');
    Route::post('/borrow-extensions/{extension}/reject', [BorrowController::class, 'rejectExtension'])->name('extensions.reject');
    
    // Late Returns
    Route::get('/late-returns', [PetugasController::class, 'lateReturns'])->name('late-returns');
    Route::get('/late-returns/{user}', [PetugasController::class, 'showLateReturn'])->name('late-returns.show');
    
    // Late Return Actions (Petugas)
    Route::post('/late-returns/{borrow}/additional-fine', [PetugasController::class, 'addAdditionalFine'])->name('late-returns.additional-fine');
    Route::post('/late-returns/{borrow}/resolve', [PetugasController::class, 'resolveLateReturn'])->name('late-returns.resolve');
    
    // Payment Confirmation (Petugas)
    Route::post('/payments/{borrow}/confirm-cash', [PetugasController::class, 'confirmCashPayment'])->name('payments.confirm-cash');
    Route::post('/payments/{borrow}/confirm-qris', [PetugasController::class, 'confirmQrisPayment'])->name('payments.confirm-qris');
    
    // Damage Reports (Petugas)
    Route::get('/damage-reports', [PetugasController::class, 'damageReports'])->name('damage-reports');
    Route::get('/damage-reports/{borrow}', [PetugasController::class, 'showDamageReport'])->name('damage-reports.show');
    Route::post('/damage-reports/{borrow}/respond', [PetugasController::class, 'respondToDamageReport'])->name('damage-reports.respond');
    Route::post('/damage-reports/{borrow}/resolve', [PetugasController::class, 'markDamageResolved'])->name('damage-reports.resolve');
    
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

/*
|--------------------------------------------------------------------------
| USER ROUTES (approved users only)
|--------------------------------------------------------------------------
*/
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
                'available_quantity' => (int) $book->calculated_available_quantity,
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

        $bestSellerBooks = Book::query()
            ->where('status', 'approved')
            ->withCount('borrows')
            ->having('borrows_count', '>', 1)
            ->orderByDesc('borrows_count')
            ->orderBy('title')
            ->limit(10)
            ->get()
            ->map(fn ($book) => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'cover' => $book->cover_image ? asset('storage/' . $book->cover_image) : 'https://placehold.co/320x480/E67E22/white?text=Book',
                'totalBorrowed' => $book->borrows_count,
                'detail_url' => route('books.show', $book->id),
            ]);

        $latestReviews = Review::with(['user:id,name', 'book:id,title'])
            ->latest()
            ->limit(5)
            ->get();

        $mapDashboardReview = function ($review) use ($user) {
            return [
                'id' => $review->id,
                'book_id' => $review->book->id,
                'review_url' => route('books.reviews', $review->book->id) . '?review=' . $review->id,
                'user' => $review->user->name,
                'user_avatar' => $review->user->profile_photo_url,
                'book_title' => $review->book->title,
                'book_cover' => $review->book->cover_image ? asset('storage/' . $review->book->cover_image) : null,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'likes_count' => $review->likes_count,
                'comments_count' => $review->comments_count,
                'bookmarks_count' => $review->bookmarks_count,
                'user_liked' => $user ? $review->isLikedBy($user->id) : false,
                'user_bookmarked' => $user ? $review->isBookmarkedBy($user->id) : false,
            ];
        };

        // Get best reviews (comment at least 500 words)
        $bestReviews = Review::with(['user', 'book', 'likes', 'comments.user', 'bookmarks'])
            ->withCount(['likes', 'comments', 'bookmarks'])
            ->whereRaw('(LENGTH(TRIM(COALESCE(comment, ""))) - LENGTH(REPLACE(TRIM(COALESCE(comment, "")), " ", "")) + 1) >= 500')
            ->orderByDesc('rating')
            ->orderByRaw('(likes_count + comments_count) DESC')
            ->limit(3)
            ->get()
            ->map($mapDashboardReview);

        // Get trending reviews (most likes + comments)
        $trendingReviews = Review::with(['user', 'book', 'likes', 'comments.user', 'bookmarks'])
            ->withCount(['likes', 'comments', 'bookmarks'])
            ->orderByRaw('(likes_count + comments_count) DESC')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map($mapDashboardReview);

        // Get recent reviews (3 most recent)
        $recentReviews = Review::with(['user', 'book', 'likes', 'comments.user', 'bookmarks'])
            ->withCount(['likes', 'comments', 'bookmarks'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map($mapDashboardReview);

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
            'bestSellerBooks' => $bestSellerBooks,
            'popularBooks' => $popularBooks,
            'latestReviews' => $latestReviews,
            'bestReviews' => $bestReviews,
            'recentReviews' => $recentReviews,
            'trendingReviews' => $trendingReviews,
            'userReviews' => $userReviews,
            'userComplaints' => $userComplaints,
            'userSuggestions' => $userSuggestions,
            'dashboardStats' => $dashboardStats,
        ]);
    })->name('dashboard');

    // Book Detail
    Route::get('/books/{book}', function (Book $book) {
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
            'available_quantity' => (int) $book->calculated_available_quantity,
            'description' => $book->description,
            'cover_image' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
            'average_rating' => round((float) ($book->reviews_avg_rating ?? 0), 1),
            'reviews_count' => $book->reviews_count ?? 0,
            'borrows_count' => $book->borrows_count ?? 0,
        ];
        
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
                'available_quantity' => (int) $otherBook->calculated_available_quantity,
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

    // Borrow Routes
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
    
    // Fine Payment Routes
    Route::get('/fines/{borrow}/report-damage', [BorrowController::class, 'showDamageReport'])->name('fine.report-damage');
    Route::post('/borrow/{borrow}/report-damage', [BorrowController::class, 'reportDamage'])->name('borrow.report-damage');
    Route::get('/fines/{borrow}/pay', [BorrowController::class, 'payFine'])->name('fine.pay');
    Route::post('/borrow/{borrow}/fine/process', [BorrowController::class, 'processPayment'])->name('fine.process');
    Route::get('/borrow/{borrow}/fine/qris', [BorrowController::class, 'showQrisPayment'])->name('fine.qris');
    Route::get('/fines/{borrow}/waiting-payment', [BorrowController::class, 'waitingPayment'])->name('fine.waiting');
    
    // QRIS Payment Confirmation
    Route::post('/borrow/{borrow}/confirm-qris-payment', [BorrowController::class, 'confirmQrisPayment'])->name('fine.confirm-qris');

    // Book Queue
    Route::post('/queue/add', [BookQueueController::class, 'store'])->name('queue.store');
    Route::delete('/queue/{queue}', [BookQueueController::class, 'cancel'])->name('queue.cancel');
    Route::get('/api/queue/position', [BookQueueController::class, 'getPosition'])->name('queue.position');
    Route::get('/api/queue/book/{book}', [BookQueueController::class, 'bookQueueInfo'])->name('queue.info');

    // Complaints
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

    // Suggestions
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

    // Reviews
    Route::post('/dashboard/reviews', function (Request $request) {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $book = Book::where('status', 'approved')->findOrFail($validated['book_id']);

        $hasReturnedBorrow = Borrow::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->where('status', 'returned')
            ->exists();

        abort_unless($hasReturnedBorrow, 403);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'image_path' => $imagePath,
            ]
        );

        return back()->with('success', 'Ulasan buku berhasil disimpan.');
    })->name('dashboard.reviews.store');

    // Book Reviews
    Route::get('/books/{book}/reviews', [ReviewController::class, 'bookReviews'])->name('books.reviews');
    Route::post('/reviews/{review}/like', [ReviewController::class, 'toggleLike'])->name('reviews.like');
    Route::post('/reviews/{review}/bookmark', [ReviewController::class, 'toggleBookmark'])->name('reviews.bookmark');
    Route::post('/reviews/{review}/comment', [ReviewController::class, 'addComment'])->name('reviews.comment.store');
    Route::delete('/review-comments/{comment}', [ReviewController::class, 'deleteComment'])->name('reviews.comment.delete');
    Route::get('/my-reviews-likes', [ReviewController::class, 'myLikedReviews'])->name('reviews.my-likes');
    Route::get('/my-reviews-bookmarks', [ReviewController::class, 'myBookmarkedReviews'])->name('reviews.my-bookmarks');
    Route::get('/my-review-comments', [ReviewController::class, 'myReviewComments'])->name('reviews.my-comments');
    Route::get('/reviews-showcase', [ReviewController::class, 'showcase'])->name('reviews.showcase');
});

/*
|--------------------------------------------------------------------------
| PROFILE ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Profile Photo Routes
    Route::post('/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyProfilePhoto'])->name('profile.photo.destroy');

    Route::middleware(['verified', 'approved'])->group(function () {
        Route::get('/scan-buku', [ScanBorrowController::class, 'index'])->name('scan-borrow.index');
        Route::post('/scan-buku/lookup', [ScanBorrowController::class, 'lookup'])->name('scan-borrow.lookup');
        Route::post('/scan-buku/pinjam', [ScanBorrowController::class, 'store'])->name('scan-borrow.store');
    });
});

/*
|--------------------------------------------------------------------------
| DEBUG ROUTES
|--------------------------------------------------------------------------
*/
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
