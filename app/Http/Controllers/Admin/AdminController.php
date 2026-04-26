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
        ]);

        $password = Str::random(8);

        $petugas = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => 'petugas',
            'status' => 'approved',
        ]);

        // Log activity
        Activity::log(auth()->id(), 'create_petugas', "Membuat akun petugas: {$petugas->email}");

        return back()->with('success', 'Akun petugas berhasil dibuat. Password: ' . $password);
    }

    public function approveUser(User $user)
    {
        if ($user->role !== 'anggota') {
            return back()->with('error', 'Hanya akun anggota yang dapat disetujui.');
        }

        $user->update(['status' => 'approved']);
        Activity::log(auth()->id(), 'approve_user', "Menyetujuai akun anggota: {$user->email}");

        return back()->with('success', 'Akun anggota telah disetujui.');
    }

    public function rejectUser(User $user)
    {
        if ($user->role !== 'anggota') {
            return back()->with('error', 'Hanya akun anggota yang dapat ditolak.');
        }

        $user->update(['status' => 'rejected']);
        Activity::log(auth()->id(), 'reject_user', "Menolak akun anggota: {$user->email}");

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
        Activity::log(auth()->id(), 'restrict_user', "Membatasi pinjaman akun: {$user->email}");

        return back()->with('success', 'Akun telah dibatasi pinjamannya.');
    }

    public function unrestrictUser(Request $request, User $user)
    {
        $user->update(['borrow_restricted' => false]);
        Activity::log(auth()->id(), 'unrestrict_user', "Menghapus pembatasan pinjaman: {$user->email}");

        return back()->with('success', 'Pembatasan pinjaman telah dicabut.');
    }

    public function destroyUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus akun admin.');
        }

        $email = $user->email;
        $user->delete();
        Activity::log(auth()->id(), 'delete_user', "Menghapus akun: {$email}");

        return back()->with('success', 'Akun berhasil dihapus.');
    }

    // Book Requests
    public function bookRequests()
    {
        $requests = BookRequest::with('petugas')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return inertia('Admin/BookRequests/Index', [
            'requests' => $requests,
        ]);
    }

    public function approveBookRequest(BookRequest $request)
    {
        // Create the book
        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'year' => $request->year,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'description' => $request->description,
            'stock' => $request->quantity,
            'available' => $request->quantity,
            'status' => 'active',
        ]);

        $request->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
        ]);

        Activity::log(auth()->id(), 'approve_book', "Menyetujui permintaan buku: {$request->title}");

        return back()->with('success', 'Permintaan buku telah disetujui dan buku ditambahkan.');
    }

    public function rejectBookRequest(Request $request, BookRequest $bookRequest)
    {
        $request->validate(['admin_note' => 'required|string']);

        $bookRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'reviewed_by' => auth()->id(),
        ]);

        Activity::log(auth()->id(), 'reject_book', "Menolak permintaan buku: {$bookRequest->title}");

        return back()->with('success', 'Permintaan buku telah ditolak.');
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
        Activity::log(auth()->id(), 'delete_review', "Menghapus ulasan buku: {$bookTitle}");

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
        $complaint->load('user', 'handler');

        return inertia('Admin/Complaints/Show', [
            'complaint' => $complaint,
        ]);
    }

    public function resolveComplaint(Request $request, Complaint $complaint)
    {
        $request->validate([
            'admin_response' => 'required|string',
        ]);

        $complaint->update([
            'status' => 'resolved',
            'admin_response' => $request->admin_response,
            'handled_by' => auth()->id(),
        ]);

        Activity::log(auth()->id(), 'resolve_complaint', "Menangani keluhan: {$complaint->subject}");

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
        $request->validate(['status' => 'required|in:pending,reviewed,implemented,rejected']);

        $suggestion->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        Activity::log(auth()->id(), 'update_suggestion', "Mengupdate status saran");

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