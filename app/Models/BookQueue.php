<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookQueue extends Model
{
    protected $table = 'book_queues';

    protected $fillable = [
        'user_id',
        'book_id',
        'position',
        'notified_at'
    ];

    protected $casts = [
        'position' => 'integer',
        'notified_at' => 'datetime'
    ];

    /**
     * Relationship: belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: belongs to Book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get user's position in queue
     */
    public static function getPosition(int $userId, int $bookId): ?int
    {
        return self::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->value('position');
    }

    /**
     * Get queue count for a book
     */
    public static function getQueueCount(int $bookId): int
    {
        return self::where('book_id', $bookId)->count();
    }

    /**
     * Get next position in queue for a book
     */
    public static function getNextPosition(int $bookId): int
    {
        $maxPosition = self::where('book_id', $bookId)
            ->max('position');
        return ($maxPosition ?? 0) + 1;
    }

    /**
     * Check if user is already in queue
     */
    public static function isUserInQueue(int $userId, int $bookId): bool
    {
        return self::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->exists();
    }
}
