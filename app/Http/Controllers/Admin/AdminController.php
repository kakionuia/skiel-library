<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Review;
use App\Models\Complaint;
use App\Models\Suggestion;
use App\Models\Activity;
use App\Models\BookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        // Stats
        $totalBooks = Book::count();
        $totalMembers = User::where('role', 'anggota')->count();
        $totalPetugas = User::where('role', 'petugas')->count();
        
        // Borrow stats - last 6 months
        $monthlyBorrows = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Borrow::whereYear('borrow_date', $month->year)
                ->whereMonth('borrow_date', $month->month)
                ->count();
            $monthlyBorrows[] = [
                'month' => $month->format('M'),
                'count' => $count
            ];
        }

        // Late returns count
        $lateReturnsCount = Borrow::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();

        // Users with late fees
        $usersWithLateFees = User::where('role', 'anggota')
            ->whereHas('borrows', function ($q) {
                $q->where('status', '!=', 'returned')->where('late_fee', '>', 0);
            })
            ->with(['borrows' => function ($q) {
                $q->where('status', '!=', 'returned')->where('late_fee', '>', 0);
            }])
            ->get();

        $totalLateFee = $usersWithLateFees->sum(function ($user) {
            return $user->borrows->sum('late_fee');
        });

        // Pending approvals
        $pendingMembers = User::where('role', 'anggota')
            ->where('status', 'pending')
            ->count();

        // Pending book requests
        $pendingBookRequests = BookRequest::where('status', 'pending')->count();

        // Pending book submissions (from petugas)
        $pendingBookSubmissions = Book::where('status', 'pending')->count();

        // Pending complaints
        $pendingComplaints = Complaint::where('status', 'pending')->count();

        return inertia('Admin/Dashboard', [
            'stats' => [
                'totalBooks' => $totalBooks,
                'totalMembers' => $totalMembers,
                'totalPetugas' => $totalPetugas,
                'lateReturnsCount' => $lateReturnsCount,
                'totalLateFee' => $totalLateFee,
                'pendingMembers' => $pendingMembers,
                'pendingBookRequests' => $pendingBookRequests,
                'pendingBookSubmissions' => $pendingBookSubmissions,
                'pendingComplaints' => $pendingComplaints,
            ],
            'monthlyBorrows' => $monthlyBorrows,
        ]);
    }

    // Accounts
    public function accounts()
    {
        $admins = User::where('role', 'admin')->get();
        $petugas = User::where('role', 'petugas')->get();
        $anggota = User::where('role', 'anggota')->get();

        return inertia('Admin/Accounts/Index', [
            'admins' => $admins,
            'petugas' => $petugas,
            'anggota' => $anggota,
        ]);
    }

    public function storePetugas(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        // Use provided password or generate random one
        $password = $request->password ?: Str::random(8);

        $petugas = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => 'petugas',
            'status' => 'approved',
        ]);

        // Log activity
        Activity::log(Auth::id(), 'create_petugas', "Membuat akun petugas: {$petugas->email}");

        return back()->with('success', 'Akun petugas berhasil dibuat. Password: ' . $password);
    }

    public function approveUser(User $user)
    {
        if ($user->role !== 'anggota') {
            return back()->with('error', 'Hanya akun anggota yang dapat disetujui.');
        }

        $user->update(['status' => 'approved']);
        Activity::log(Auth::id(), 'approve_user', "Menyetujuai akun anggota: {$user->email}");

        return back()->with('success', 'Akun anggota telah disetujui.');
    }

    public function rejectUser(User $user)
    {
        if ($user->role !== 'anggota') {
            return back()->with('error', 'Hanya akun anggota yang dapat ditolak.');
        }

        $user->update(['status' => 'rejected']);
        Activity::log(Auth::id(), 'reject_user', "Menolak akun anggota: {$user->email}");

        return back()->with('success', 'Akun anggota telah ditolak.');
    }

    public function showUser(User $user)
    {
        $user->load(['borrows.book', 'reviews.book']);
        $recentBorrows = $user->borrows()->with('book')->orderBy('created_at', 'desc')->limit(3)->get();

        return inertia('Admin/Accounts/Show', [
            'user' => $user,
            'recentBorrows' => $recentBorrows,
        ]);
    }

    public function showPetugas(User $petugas)
    {
        $petugas->load('activities');
        $activities = $petugas->activities()->orderBy('created_at', 'desc')->limit(20)->get();

        return inertia('Admin/Accounts/PetugasShow', [
            'petugas' => $petugas,
            'activities' => $activities,
        ]);
    }

    public function restrictUser(Request $request, User $user)
    {
        $user->update(['borrow_restricted' => true]);
        Activity::log(Auth::id(), 'restrict_user', "Membatasi pinjaman akun: {$user->email}");

        return back()->with('success', 'Akun telah dibatasi pinjamannya.');
    }

    public function unrestrictUser(Request $request, User $user)
    {
        $user->update(['borrow_restricted' => false]);
        Activity::log(Auth::id(), 'unrestrict_user', "Menghapus pembatasan pinjaman: {$user->email}");

        return back()->with('success', 'Pembatasan pinjaman telah dicabut.');
    }

    public function destroyUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus akun admin.');
        }

        $email = $user->email;
        $user->delete();
        Activity::log(Auth::id(), 'delete_user', "Menghapus akun: {$email}");

        return back()->with('success', 'Akun berhasil dihapus.');
    }

    // Book Requests
    public function bookRequests()
    {
        $requests = BookRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return inertia('Admin/BookRequests/Index', [
            'requests' => $requests,
        ]);
    }

    public function approveBookRequest(BookRequest $bookRequest)
    {
        // Create the book from the request
        Book::create([
            'book_id' => 'BK-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'title' => $bookRequest->title,
            'author' => $bookRequest->author,
            'isbn' => $bookRequest->isbn,
            'publisher' => $bookRequest->publisher ?? '-',
            'year' => $bookRequest->year,
            'category' => $bookRequest->category ?? 'Umum',
            'description' => $bookRequest->description,
            'quantity' => $bookRequest->quantity ?? 1,
            'available_quantity' => $bookRequest->quantity ?? 1,
            'status' => 'approved',
            'created_by' => $bookRequest->petugas_id,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Update the request status
        $bookRequest->update([
            'status' => 'approved',
        ]);

        Activity::log(Auth::id(), 'approve_book', "Menyetujui dan menambahkan buku: {$bookRequest->title}");

        return back()->with('success', 'Buku telah ditambahkan ke perpustakaan.');
    }

    public function rejectBookRequest(Request $request, BookRequest $bookRequest)
    {
        $bookRequest->update([
            'status' => 'rejected',
        ]);

        Activity::log(Auth::id(), 'reject_book', "Menolak permintaan buku: {$bookRequest->title}");

        return back()->with('success', 'Permintaan buku telah ditolak.');
    }

    // Pengajuan Buku (Book Submissions from Petugas)
    public function bookSubmissions()
    {
        $submissions = Book::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Transform the submissions
        $submissions->getCollection()->transform(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'category' => $book->category,
                'quantity' => $book->quantity,
                'status' => $book->status,
                'cover_image' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
                'created_at' => $book->created_at,
                'petugas' => $book->user ? [
                    'id' => $book->user->id,
                    'name' => $book->user->name,
                    'email' => $book->user->email,
                ] : null,
            ];
        });

        return inertia('Admin/BookSubmissions/Index', [
            'submissions' => $submissions,
        ]);
    }

    public function showBookSubmission(Book $book)
    {
        // Make sure it's a pending book
        if ($book->status !== 'pending') {
            return back()->with('error', 'Pengajuan buku tidak ditemukan.');
        }

        $book->load('user', 'approver');
        $createdBy = $book->user;

        return inertia('Admin/BookSubmissions/Show', [
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'year' => $book->year,
                'isbn' => $book->isbn,
                'category' => $book->category,
                'quantity' => $book->quantity,
                'description' => $book->description,
                'cover_image' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
                'status' => $book->status,
                'created_at' => $book->created_at,
                'approved_at' => $book->approved_at,
                'created_by' => $book->created_by,
                'approved_by_user' => $book->approver ? [
                    'id' => $book->approver->id,
                    'name' => $book->approver->name,
                    'email' => $book->approver->email,
                ] : null,
            ],
            'createdBy' => $createdBy ? [
                'id' => $createdBy->id,
                'name' => $createdBy->name,
                'email' => $createdBy->email,
                'role' => $createdBy->role,
            ] : null,
        ]);
    }

    public function approveBookSubmission(Book $book)
    {
        // Make sure it's a pending book
        if ($book->status !== 'pending') {
            return back()->with('error', 'Pengajuan buku tidak valid.');
        }

        $book->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Activity::log(Auth::id(), 'approve_book_submission', "Menyetujui pengajuan buku dari petugas: {$book->title}");

        return back()->with('success', 'Pengajuan buku telah disetujui dan ditambahkan ke perpustakaan.');
    }

    public function rejectBookSubmission(Request $request, Book $book)
    {
        // Make sure it's a pending book
        if ($book->status !== 'pending') {
            return back()->with('error', 'Pengajuan buku tidak valid.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $book->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Activity::log(Auth::id(), 'reject_book_submission', "Menolak pengajuan buku: {$book->title}");

        return back()->with('success', 'Pengajuan buku telah ditolak.');
    }

    // Late Returns
    public function lateReturns()
    {
        $lateBorrows = Borrow::with(['user', 'book'])
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        // Group by user
        $usersWithLate = [];
        foreach ($lateBorrows as $borrow) {
            $userId = $borrow->user_id;
            if (!isset($usersWithLate[$userId])) {
                $usersWithLate[$userId] = [
                    'user' => $borrow->user,
                    'borrows' => [],
                    'totalFee' => 0,
                ];
            }
            $usersWithLate[$userId]['borrows'][] = $borrow;
            $usersWithLate[$userId]['totalFee'] += $borrow->late_fee;
        }

        return inertia('Admin/LateReturns/Index', [
            'lateBorrows' => $lateBorrows,
            'usersWithLate' => $usersWithLate,
        ]);
    }

    public function showLateReturn(User $user)
    {
        $user->load(['borrows.book' => function ($q) {
            $q->where('status', 'borrowed')->orWhere('due_date', '<', now());
        }]);

        $lateBorrows = $user->borrows()
            ->with('book')
            ->where(function ($q) {
                $q->where('status', 'borrowed')->where('due_date', '<', now());
            })
            ->get();

        return inertia('Admin/LateReturns/Show', [
            'user' => $user,
            'lateBorrows' => $lateBorrows,
        ]);
    }

    // Reviews
    public function reviews()
    {
        $reviews = Review::with(['user', 'book'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return inertia('Admin/Reviews/Index', [
            'reviews' => $reviews,
        ]);
    }

    public function destroyReview(Review $review)
    {
        $bookTitle = $review->book->title;
        $review->delete();
        Activity::log(Auth::id(), 'delete_review', "Menghapus ulasan buku: {$bookTitle}");

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }

    // Complaints
    public function complaints()
    {
        $complaints = Complaint::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return inertia('Admin/Complaints/Index', [
            'complaints' => $complaints,
        ]);
    }

    public function showComplaint(Complaint $complaint)
    {
        $complaint->load('user');

        return inertia('Admin/Complaints/Show', [
            'complaint' => $complaint,
        ]);
    }

    public function resolveComplaint(Request $request, Complaint $complaint)
    {
        $complaint->update([
            'status' => 'resolved',
        ]);

        Activity::log(Auth::id(), 'resolve_complaint', "Menangani keluhan: {$complaint->subject}");

        return back()->with('success', 'Keluhan telah ditangani.');
    }

    // Suggestions
    public function suggestions()
    {
        $suggestions = Suggestion::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return inertia('Admin/Suggestions/Index', [
            'suggestions' => $suggestions,
        ]);
    }

    public function updateSuggestionStatus(Request $request, Suggestion $suggestion)
    {
        // Suggestions table doesn't have status column, just log the activity
        Activity::log(Auth::id(), 'update_suggestion', "Mengupdate status saran");

        return back()->with('success', 'Status saran telah diupdate.');
    }

    // Activity Logs
    public function activityLogs(Request $request)
    {
        $role = $request->get('role', 'all');
        
        $query = Activity::with('user')->orderBy('created_at', 'desc');
        
        if ($role !== 'all') {
            $query->whereHas('user', function ($q) use ($role) {
                $q->where('role', $role);
            });
        }
        
        $activities = $query->paginate(50);

        return inertia('Admin/ActivityLogs/Index', [
            'activities' => $activities,
            'filters' => ['role' => $role],
        ]);
    }
}
