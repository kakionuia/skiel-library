<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookQueue;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookQueueController extends Controller
{
    /**
     * Add user to book queue
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $user = auth()->user();
        $book = Book::findOrFail($request->book_id);

        // Check if user is already in queue
        if (BookQueue::isUserInQueue($user->id, $book->id)) {
            return back()->with('error', 'Anda sudah berada dalam antrian untuk buku ini');
        }

        // Check if user already has an active borrow of this book
        $existingBorrow = $user->borrows()
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->exists();

        if ($existingBorrow) {
            return back()->with('error', 'Anda sudah meminjam buku ini');
        }

        // Get next position in queue
        $position = BookQueue::getNextPosition($book->id);

        // Create queue entry
        BookQueue::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'position' => $position
        ]);

        // Log activity
        Activity::log(Auth::id(), 'queue_join', "Bergabung dalam antrian untuk buku: {$book->title} (Posisi: #{$position})");

        return back()->with('success', 'Anda telah berhasil bergabung dalam antrian. Posisi Anda: #' . $position);
    }

    /**
     * Remove user from queue
     */
    public function cancel(BookQueue $queue)
    {
        // Authorize
        $this->authorize('delete', $queue);

        $bookId = $queue->book_id;
        $cancelledPosition = $queue->position;

        // Delete from queue
        $queue->delete();

        // Reorder positions for remaining users
        BookQueue::where('book_id', $bookId)
            ->where('position', '>', $cancelledPosition)
            ->decrement('position');

        // Log activity
        Activity::log($queue->user_id, 'queue_cancel', "Membatalkan antrian untuk buku: {$queue->book->title}");

        return back()->with('success', 'Anda telah keluar dari antrian');
    }

    /**
     * Notify first person in queue when book is available
     */
    public static function notifyNextInQueue(int $bookId): void
    {
        // Get first person in queue
        $firstInQueue = BookQueue::where('book_id', $bookId)
            ->orderBy('position')
            ->first();

        if (!$firstInQueue) {
            return;
        }

        $user = $firstInQueue->user;
        $book = $firstInQueue->book;

        // Send notification (implement via email, SMS, or in-app)
        // For now, we'll just mark as notified
        $firstInQueue->update([
            'notified_at' => now()
        ]);

        // You can implement actual notification here
        // Example: Mail::send(...), SMS service, etc.
    }

    /**
     * Get user's queue position for a book
     */
    public function getPosition(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $user = auth()->user();
        $position = BookQueue::getPosition($user->id, $request->book_id);

        return response()->json([
            'in_queue' => $position !== null,
            'position' => $position,
            'queue_count' => BookQueue::getQueueCount($request->book_id)
        ]);
    }

    /**
     * Get queue info for a book
     */
    public function bookQueueInfo(Book $book)
    {
        $user = auth()->user();
        $queue = BookQueue::where('book_id', $book->id)
            ->orderBy('position')
            ->with('user')
            ->get();

        $userPosition = BookQueue::getPosition($user->id, $book->id);
        $queueCount = $queue->count();

        return response()->json([
            'book_id' => $book->id,
            'queue_count' => $queueCount,
            'user_in_queue' => $userPosition !== null,
            'user_position' => $userPosition,
            'queue' => $queue->map(fn ($item) => [
                'position' => $item->position,
                'user_name' => $item->user->name,
                'created_at' => $item->created_at
            ])
        ]);
    }

    /**
     * Show user's queue page
     */
    public function myQueue()
    {
        $user = auth()->user();
        
        $queuedBooks = BookQueue::with(['book', 'user'])
            ->where('user_id', $user->id)
            ->orderBy('position')
            ->get()
            ->map(fn ($queue) => [
                'id' => $queue->id,
                'book_id' => $queue->book_id,
                'position' => $queue->position,
                'notified_at' => $queue->notified_at,
                'created_at' => $queue->created_at,
                'book' => [
                    'id' => $queue->book?->id,
                    'title' => $queue->book?->title,
                    'author' => $queue->book?->author,
                    'cover_image' => $queue->book?->cover_image ? asset('storage/' . $queue->book->cover_image) : null,
                ]
            ]);

        $stats = [
            'waitingCount' => $queuedBooks->filter(fn ($q) => !$q['notified_at'])->count(),
            'notifiedCount' => $queuedBooks->filter(fn ($q) => $q['notified_at'])->count(),
        ];

        return Inertia::render('User/Rak/Queue', [
            'queuedBooks' => $queuedBooks,
            'stats' => $stats,
        ]);
    }
}

