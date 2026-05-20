<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book;
use App\Models\ReviewLike;
use App\Models\ReviewBookmark;
use App\Models\ReviewComment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function bookReviews(Book $book)
    {
        $reviews = $book->reviews()
            ->with(['user', 'likes', 'comments.user', 'bookmarks'])
            ->withCount(['likes', 'comments', 'bookmarks'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $userId = Auth::id();
        
        // Add user interaction flags to each review
        $reviews->getCollection()->transform(function ($review) use ($userId) {
            $review->user_liked = $userId ? $review->isLikedBy($userId) : false;
            $review->user_bookmarked = $userId ? $review->isBookmarkedBy($userId) : false;
            return $review;
        });

        return Inertia::render('User/BookReviews', [
            'book' => $book,
            'reviews' => $reviews,
            'totalReviews' => $book->reviews()->count(),
        ]);
    }

    public function toggleLike(Review $review)
    {
        $userId = Auth::id();

        $exists = ReviewLike::where('user_id', $userId)
            ->where('review_id', $review->id)
            ->first();

        if ($exists) {
            $exists->delete();
            $liked = false;
        } else {
            ReviewLike::create([
                'user_id' => $userId,
                'review_id' => $review->id,
            ]);
            $liked = true;
        }

        return back()->with('success', $liked ? 'Ulasan disukai!' : 'Suka ulasan dibatalkan');
    }

    public function toggleBookmark(Review $review)
    {
        $userId = Auth::id();

        $exists = ReviewBookmark::where('user_id', $userId)
            ->where('review_id', $review->id)
            ->first();

        if ($exists) {
            $exists->delete();
            $bookmarked = false;
        } else {
            ReviewBookmark::create([
                'user_id' => $userId,
                'review_id' => $review->id,
            ]);
            $bookmarked = true;
        }

        return back()->with('success', $bookmarked ? 'Ulasan disimpan!' : 'Simpan ulasan dibatalkan');
    }

    public function addComment(Request $request, Review $review)
    {
        $validated = $request->validate([
            'comment' => 'required|string|min:3|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('review-comments', 'public');
        }

        $comment = ReviewComment::create([
            'user_id' => Auth::id(),
            'review_id' => $review->id,
            'comment' => $validated['comment'],
            'image_path' => $imagePath,
        ])->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'comments_count' => $review->comments()->count(),
            ]);
        }

        return back()->with('success', 'Komentar ditambahkan!');
    }

    public function deleteComment(ReviewComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $review = $comment->review;
        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'comments_count' => $review->comments()->count(),
            ]);
        }

        return back()->with('success', 'Komentar dihapus');
    }

    public function myLikedReviews()
    {
        $reviews = ReviewLike::where('user_id', Auth::id())
            ->with(['review.user', 'review.book', 'review.comments.user', 'review.bookmarks', 'review.likes'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $userId = Auth::id();
        
        $reviews->getCollection()->transform(function ($like) use ($userId) {
            $like->review->user_liked = true;
            $like->review->user_bookmarked = $like->review->isBookmarkedBy($userId);
            return $like;
        });

        return Inertia::render('User/MyReviewsLikes', [
            'reviewLikes' => $reviews,
        ]);
    }

    public function myBookmarkedReviews()
    {
        $reviews = ReviewBookmark::where('user_id', Auth::id())
            ->with(['review.user', 'review.book', 'review.comments.user', 'review.bookmarks', 'review.likes'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $userId = Auth::id();
        
        $reviews->getCollection()->transform(function ($bookmark) use ($userId) {
            $bookmark->review->user_liked = $bookmark->review->isLikedBy($userId);
            $bookmark->review->user_bookmarked = true;
            return $bookmark;
        });

        return Inertia::render('User/MyReviewsBookmarks', [
            'reviewBookmarks' => $reviews,
        ]);
    }

    public function myReviewComments()
    {
        $comments = ReviewComment::where('user_id', Auth::id())
            ->with(['review.user', 'review.book', 'user', 'review.comments.user', 'review.bookmarks', 'review.likes'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $userId = Auth::id();
        
        $comments->getCollection()->transform(function ($comment) use ($userId) {
            $comment->review->user_liked = $comment->review->isLikedBy($userId);
            $comment->review->user_bookmarked = $comment->review->isBookmarkedBy($userId);
            return $comment;
        });

        return Inertia::render('User/MyReviewComments', [
            'reviewComments' => $comments,
        ]);
    }

    public function showcase()
    {
        $userId = Auth::id();

        // Get latest reviews (from all books)
        $latestReviews = Review::with(['user', 'book', 'likes', 'comments.user', 'bookmarks'])
            ->withCount(['likes', 'comments', 'bookmarks'])
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($review) use ($userId) {
                $review->user_liked = $userId ? $review->isLikedBy($userId) : false;
                $review->user_bookmarked = $userId ? $review->isBookmarkedBy($userId) : false;
                return $review;
            });

        // Get best reviews (ordered by likes count)
        $bestReviews = Review::with(['user', 'book', 'likes', 'comments.user', 'bookmarks'])
            ->withCount(['likes', 'comments', 'bookmarks'])
            ->orderBy('rating', 'desc')
            ->orderBy('likes_count', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($review) use ($userId) {
                $review->user_liked = $userId ? $review->isLikedBy($userId) : false;
                $review->user_bookmarked = $userId ? $review->isBookmarkedBy($userId) : false;
                return $review;
            });

        return Inertia::render('User/ReviewsShowcase', [
            'latestReviews' => $latestReviews,
            'bestReviews' => $bestReviews,
        ]);
    }
}
