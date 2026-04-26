<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\BookRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PetugasController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $totalBooks = Book::count();
        $availableBooks = Book::sum('available');
        $borrowedBooks = Borrow::where('status', 'borrowed')->count();
        $lateReturns = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();
        $totalMembers = User::where('role', 'anggota')->where('status', 'approved')->count();
        
        // My book requests
        $myBookRequests = BookRequest::where('petugas_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent activities
        $recentActivities = Activity::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return inertia('Petugas/Dashboard', [
            'stats' => [
                'totalBooks' => $totalBooks,
                'availableBooks' => $availableBooks,
                'borrowedBooks' => $borrowedBooks,
                'lateReturns' => $lateReturns,
                'totalMembers' => $totalMembers,
            ],
            'myBookRequests' => $myBookRequests,
            'recentActivities' => $recentActivities,
        ]);
    }

    // Books Management
    public function books()
    {
        $books = Book::orderBy('created_at', 'desc')->paginate(20);

        return inertia('Petugas/Books/Index', [
            'books' => $books,
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
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'year' => $request->year,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'stock' => $request->stock ?? 1,
            'available' => $request->stock ?? 1,
            'description' => $request->description,
        ]);

        Activity::log(auth()->id(), 'create', "Menambahkan buku: {$book->title}");

        return back()->with('success', 'Buku berhasil ditambahkan.');
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
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $oldStock = $book->stock;
        $newStock = $request->stock ?? $book->stock;
        $stockDiff = $newStock - $oldStock;

        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'year' => $request->year,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'stock' => $newStock,
            'available' => $book->available + $stockDiff,
            'description' => $request->description,
        ]);

        Activity::log(auth()->id(), 'update', "Mengubah data buku: {$book->title}");

        return redirect()->route('petugas.books')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroyBook(Book $book)
    {
        $title = $book->title;
        $book->delete();

        Activity::log(auth()->id(), 'delete', "Menghapus buku: {$title}");

        return back()->with('success', 'Buku berhasil dihapus.');
    }

    // Add Book Request
    public function bookRequests()
    {
        $requests = BookRequest::where('petugas_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

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
            'petugas_id' => auth()->id(),
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

        Activity::log(auth()->id(), 'request_book', "Mengajukan permintaan buku: {$request->title}");

        return back()->with('success', 'Permintaan buku telah diajukan ke admin.');
    }

    // Late Returns
    public function lateReturns()
    {
        $lateBorrows = Borrow::with(['user', 'book'])
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        // Summary
        $totalLateStudents = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->distinct('user_id')
            ->count('user_id');
        $totalLateBooks = $lateBorrows->total();
        $totalLateFee = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->sum('late_fee');

        return inertia('Petugas/LateReturns/Index', [
            'lateReturns' => $lateBorrows,
            'summary' => [
                'totalLateStudents' => $totalLateStudents,
                'totalLateBooks' => $totalLateBooks,
                'totalLateFee' => $totalLateFee,
            ],
        ]);
    }

    // Activity Logs
    public function activityLogs()
    {
        $activities = Activity::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return inertia('Petugas/ActivityLogs/Index', [
            'activities' => $activities,
        ]);
    }
}