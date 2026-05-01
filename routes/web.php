<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Petugas\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaitingController;
use App\Http\Controllers\Auth\StatusCheckController;
use App\Http\Controllers\Auth\AccountDeletionController;
use Illuminate\Foundation\Application;
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
    Route::post('/book-requests/{request}/approve', [AdminController::class, 'approveBookRequest'])->name('book-requests.approve');
    Route::post('/book-requests/{request}/reject', [AdminController::class, 'rejectBookRequest'])->name('book-requests.reject');
    
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
        
        // Get active borrows (currently borrowed)
        $activeBorrows = \App\Models\Borrow::with('book')
            ->where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->orderBy('due_date', 'asc')
            ->get();
        
        // Get recent borrows (last 5)
        $recentBorrows = \App\Models\Borrow::with('book')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Count overdue
        $overdueCount = \App\Models\Borrow::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();
        
        // Total fines (from overdue items)
        $totalFines = \App\Models\Borrow::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->sum('late_fee') ?? 0;
        
        // Total borrows (count all)
        $totalBorrows = \App\Models\Borrow::where('user_id', $user->id)->count();
        
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
        ]);
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
