<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ScanBorrowController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('User/ScanBorrow');
    }

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'barcode_code' => ['required', 'string', 'max:255'],
        ]);

        $book = Book::where('barcode_code', trim($validated['barcode_code']))
            ->where('status', 'approved')
            ->first();

        if (!$book) {
            return response()->json([
                'valid' => false,
                'message' => 'Barcode tidak ditemukan pada koleksi buku.',
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'book' => $this->bookPayload($book),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'barcode_code' => ['required', 'string', 'max:255'],
        ]);

        if ($user->borrow_restricted) {
            return response()->json([
                'message' => 'Akun Anda sedang dibatasi untuk meminjam buku.',
            ], 422);
        }

        $hasOverdue = Borrow::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_date', '<', now())
            ->exists();

        if ($hasOverdue) {
            return response()->json([
                'message' => 'Anda memiliki peminjaman yang terlambat. Harap kembalikan terlebih dahulu.',
            ], 422);
        }

        $book = Book::where('barcode_code', trim($validated['barcode_code']))
            ->where('status', 'approved')
            ->first();

        if (!$book) {
            return response()->json([
                'message' => 'Barcode tidak valid atau buku belum disetujui.',
            ], 404);
        }

        if ($book->calculated_available_quantity < 1) {
            return response()->json([
                'message' => 'Stok buku tidak tersedia.',
                'book' => $this->bookPayload($book),
            ], 422);
        }

        $existingBorrow = Borrow::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->exists();

        if ($existingBorrow) {
            return response()->json([
                'message' => 'Anda masih memiliki peminjaman aktif untuk buku ini.',
                'book' => $this->bookPayload($book),
            ], 422);
        }

        $activeBorrows = Borrow::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->count();

        if ($activeBorrows >= 3) {
            return response()->json([
                'message' => 'Anda sudah mencapai batas maksimal peminjaman (3 buku).',
            ], 422);
        }

        $borrow = Borrow::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'quantity' => 1,
            'status' => 'pending',
            'notes' => 'Diajukan melalui scan barcode: ' . $book->barcode_code,
        ]);

        Activity::log($user->id, 'borrow_scan_request', "Mengajukan peminjaman via scan barcode: {$book->title}");

        return response()->json([
            'message' => 'Pengajuan peminjaman berhasil dibuat.',
            'redirect' => route('borrow.my-loans'),
            'borrow_id' => $borrow->id,
            'book' => $this->bookPayload($book),
        ], 201);
    }

    private function bookPayload(Book $book): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'publisher' => $book->publisher,
            'year' => $book->year,
            'isbn' => $book->isbn,
            'barcode_code' => $book->barcode_code,
            'category' => $book->category ?: 'Umum',
            'available_quantity' => (int) $book->calculated_available_quantity,
            'cover_image' => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
        ];
    }
}
