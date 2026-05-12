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

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalBooks' => $totalBooks,
                'totalBorrows' => $totalBorrows,
                'pendingApprovals' => $pendingApprovals,
                'lateReturns' => $lateReturns,
                'totalMembers' => $totalMembers,
                'totalPetugas' => $totalPetugas,
                'pendingMembers' => $pendingMembers,
            ],
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

        Book::create($validated);

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

        return redirect()->route('admin.books')->with('success', 'Buku berhasil diperbarui');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function destroyCategory(Request $request, $category)
    {
        $cat = Category::where('name', $category)->first();
        if ($cat) {
            $cat->delete();
        }
        return back()->with('success', 'Kategori berhasil dihapus');
    }

    public function destroyBook(Book $book)
    {
        $book->delete();

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

        return back()->with('success', 'Permintaan buku disetujui');
    }

    public function rejectBookRequest(BookRequest $bookRequest)
    {
        $bookRequest->update(['status' => 'rejected']);

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

        return back()->with('success', 'Buku disetujui');
    }

    public function rejectBookSubmission(Book $book)
    {
        if (request()->has('rejection_reason')) {
            $book->update([
                'status' => 'rejected',
                'rejection_reason' => request('rejection_reason'),
            ]);
        } else {
            $book->update(['status' => 'rejected']);
        }

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
        $review->delete();

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

        return back()->with('success', 'Saran diproses');
    }

    // Late Returns
    public function lateReturns()
    {
        $lateReturns = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->with('user', 'book')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/LateReturns/Index', [
            'lateReturns' => $lateReturns,
        ]);
    }

    public function showLateReturn(User $user)
    {
        $lateReturns = $user->borrows()
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->with('book')
            ->get();

        return Inertia::render('Admin/UserLateReturns', [
            'user' => $user,
            'lateReturns' => $lateReturns,
        ]);
    }

    // Activity Logs
    public function activityLogs()
    {
        $logs = Activity::latest()->paginate(20);

        return Inertia::render('Admin/ActivityLogs/Index', [
            'logs' => $logs,
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

        User::create($validated);

        return back()->with('success', 'Petugas berhasil ditambahkan');
    }

    public function approveUser(User $user)
    {
        $user->update(['status' => 'approved']);

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

        return back()->with('success', 'Anggota dibatasi peminjaman');
    }

    public function unrestrictUser(User $user)
    {
        $user->update(['borrow_restricted' => false]);

        return back()->with('success', 'Pembatasan anggota dihapus');
    }

    public function destroyUser(User $user)
    {
        $user->delete();

        return back()->with('success', 'Anggota dihapus');
    }
    public function createBook()
    {
        $categories = Category::all();
        return Inertia::render('Admin/Books/Create', [
            'categories' => $categories,
        ]);
    }
}
