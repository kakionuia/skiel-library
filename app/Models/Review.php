<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment',
        'image_path',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    protected $withCount = ['likes', 'comments', 'bookmarks'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function likes()
    {
        return $this->hasMany(ReviewLike::class);
    }

    public function comments()
    {
        return $this->hasMany(ReviewComment::class)->orderBy('created_at', 'desc');
    }

    public function bookmarks()
    {
        return $this->hasMany(ReviewBookmark::class);
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isBookmarkedBy($userId)
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    public function scopeByBook($query, $bookId)
    {
        return $query->where('book_id', $bookId)->orderBy('created_at', 'desc');
    }
}