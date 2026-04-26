<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Petugas\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaitingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/waiting', [WaitingController::class, 'index'])->name('waiting');

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
    Route::post('/books', [PetugasController::class, 'storeBook'])->name('books.store');
    Route::get('/books/{book}/edit', [PetugasController::class, 'editBook'])->name('books.edit');
    Route::put('/books/{book}', [PetugasController::class, 'updateBook'])->name('books.update');
    Route::delete('/books/{book}', [PetugasController::class, 'destroyBook'])->name('books.destroy');
    
    // Book Requests
    Route::get('/book-requests', [PetugasController::class, 'bookRequests'])->name('book-requests');
    Route::post('/book-requests', [PetugasController::class, 'storeBookRequest'])->name('book-requests.store');
    
    // Late Returns
    Route::get('/late-returns', [PetugasController::class, 'lateReturns'])->name('late-returns');
    
    // Activity Logs
    Route::get('/activity-logs', [PetugasController::class, 'activityLogs'])->name('activity-logs');
});

// User Routes (must be approved)
Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
