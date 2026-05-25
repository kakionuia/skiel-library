<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\LiteracyEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ArticleController extends Controller
{
    /**
     * ADMIN: Display all articles for management
     */
    public function admin(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') abort(403, 'Unauthorized');
        
        $articles = Article::with(['author:id,name', 'approver:id,name'])
            ->when($request->search, function($query) use ($request) {
                return $query->where('title', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('content', 'LIKE', '%' . $request->search . '%');
            })
            ->when($request->status, function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->category, function($query) use ($request) {
                return $query->where('category', $request->category);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles,
            'filters' => $request->only(['search', 'status', 'category'])
        ]);
    }

    /**
     * ADMIN: Show create form
     */
    public function adminCreate()
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') abort(403, 'Unauthorized');
        
        return Inertia::render('Admin/Articles/Create');
    }

    /**
     * ADMIN: Store article
     */
    public function adminStore(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') abort(403, 'Unauthorized');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|in:artikel,berita,event',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'in:draft,pending,approved'
        ]);

        $slug = Article::generateSlug($validated['title']);
        $validated['slug'] = $slug;
        $validated['author_id'] = Auth::id();

        if ($validated['status'] === 'approved') {
            $validated['published_at'] = now();
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        Article::create($validated);

        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil dibuat');
    }

    /**
     * ADMIN: Show edit form
     */
    public function adminEdit(Article $article)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') abort(403, 'Unauthorized');
        
        return Inertia::render('Admin/Articles/Edit', [
            'article' => $article
        ]);
    }

    /**
     * ADMIN: Update article
     */
    public function adminUpdate(Request $request, Article $article)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') abort(403, 'Unauthorized');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|in:artikel,berita,event',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'in:draft,pending,approved'
        ]);

        if ($validated['title'] !== $article->title) {
            $validated['slug'] = Article::generateSlug($validated['title']);
        }

        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                \Storage::disk('public')->delete($article->featured_image);
            }
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        if ($article->status !== 'approved' && $validated['status'] === 'approved') {
            $validated['published_at'] = now();
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil diperbarui');
    }

    /**
     * ADMIN: Delete article
     */
    public function adminDestroy(Article $article)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') abort(403, 'Unauthorized');
        
        if ($article->featured_image) {
            \Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        return back()->with('success', 'Artikel berhasil dihapus');
    }

    /**
     * ADMIN: Approve article
     */
    public function approve(Article $article)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') abort(403, 'Unauthorized');
        
        $article->update([
            'status' => 'approved',
            'published_at' => now(),
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return back()->with('success', 'Artikel berhasil disetujui');
    }

    /**
     * ADMIN: Reject article
     */
    public function reject(Request $request, Article $article)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') abort(403, 'Unauthorized');
        
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $article->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return back()->with('success', 'Artikel berhasil ditolak');
    }

    /**
     * FRONTEND: Display published articles list (HALAMAN ALL ARTICLES)
     */
    public function index(Request $request)
    {
        $articles = Article::published()
            ->with('author:id,name')
            ->when($request->search, function($query) use ($request) {
                return $query->where('title', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('excerpt', 'LIKE', '%' . $request->search . '%');
            })
            ->when($request->category && !in_array($request->category, ['all', 'event']), function($query) use ($request) {
                return $query->where('category', $request->category);
            })
            ->when($request->sort, function($query) use ($request) {
                switch ($request->sort) {
                    case 'oldest':
                        return $query->orderBy('published_at', 'asc');
                    case 'title_asc':
                        return $query->orderBy('title', 'asc');
                    case 'title_desc':
                        return $query->orderBy('title', 'desc');
                    case 'latest':
                    default:
                        return $query->orderBy('published_at', 'desc');
                }
            }, function($query) {
                return $query->orderBy('published_at', 'desc');
            })
            ->get()
            ->map(function ($article) {
                if ($article->featured_image) {
                    $article->featured_image = asset('storage/' . $article->featured_image);
                }
                // Calculate read time
                $wordCount = str_word_count(strip_tags($article->content ?? ''));
                $article->readTime = max(1, ceil($wordCount / 200));
                return $article;
            });

        $events = LiteracyEvent::with('winner:id,name,profile_photo_path')
            ->withCount('participants')
            ->when($request->search, function ($query) use ($request) {
                return $query->where('title', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            })
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('starts_at', 'desc')
            ->get()
            ->map(fn ($event) => [
                'id' => 'event-' . $event->id,
                'event_id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'excerpt' => $event->description,
                'category' => 'event',
                'event_category' => $event->category,
                'featured_image' => $event->image_url,
                'published_at' => $event->starts_at,
                'starts_at' => $event->starts_at,
                'ends_at' => $event->ends_at,
                'status_label' => $event->status_label,
                'participants_count' => $event->participants_count,
                'winner' => $event->winner,
                'is_event' => true,
                'readTime' => null,
            ]);

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'events' => $events,
            'filters' => $request->only(['search', 'category', 'sort', 'page'])
        ]);
    }

    /**
     * FRONTEND: Welcome page data
     */
    public function welcome()
    {
        $latestArticles = Article::published()
            ->with('author:id,name')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($article) {
                if ($article->featured_image) {
                    $article->featured_image = asset('storage/' . $article->featured_image);
                }
                return $article;
            });

        $latestEvents = LiteracyEvent::with('winner:id,name,profile_photo_path')
            ->withCount('participants')
            ->latest('starts_at')
            ->limit(10)
            ->get()
            ->map(fn ($event) => [
                'id' => 'event-' . $event->id,
                'event_id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'excerpt' => $event->description,
                'category' => 'event',
                'event_category' => $event->category,
                'featured_image' => $event->image_url,
                'published_at' => $event->starts_at,
                'starts_at' => $event->starts_at,
                'ends_at' => $event->ends_at,
                'status_label' => $event->status_label,
                'participants_count' => $event->participants_count,
                'is_event' => true,
            ]);

        return Inertia::render('Welcome', [
            'latestArticles' => $latestArticles,
            'latestEvents' => $latestEvents,
        ]);
    }

    /**
     * FRONTEND: Show single article
     */
    public function show(Article $article)
    {
        if ($article->status !== 'approved' && (!Auth::check() || !Auth::user()->is_admin)) {
            abort(404);
        }

        // Set full URL for featured image
        if ($article->featured_image) {
            $article->featured_image = asset('storage/' . $article->featured_image);
        }

        $relatedArticles = Article::published()
            ->where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        if ($relatedArticles->count() < 3) {
            $fallbackArticles = Article::published()
                ->where('id', '!=', $article->id)
                ->whereNotIn('id', $relatedArticles->pluck('id'))
                ->orderBy('published_at', 'desc')
                ->limit(3 - $relatedArticles->count())
                ->get();

            $relatedArticles = $relatedArticles->concat($fallbackArticles)->values();
        }

        $relatedArticles = $relatedArticles->map(function ($item) {
            if ($item->featured_image) {
                $item->featured_image = asset('storage/' . $item->featured_image);
            }
            return $item;
        });

        $article->load([
            'author',
            'comments.user:id,name,profile_photo_path',
            'comments.replies.user:id,name,profile_photo_path',
        ]);
        $article->comments_count = $article->comments->sum(fn ($comment) => 1 + $comment->replies->count());

        return Inertia::render('ArticleDetail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles
        ]);
    }

    public function storeComment(Request $request, Article $article)
    {
        if ($article->status !== 'approved') {
            abort(404);
        }

        $validated = $request->validate([
            'comment' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:article_comments,id',
        ]);

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId) {
            $parent = ArticleComment::where('article_id', $article->id)->findOrFail($parentId);
            $parentId = $parent->parent_id ?: $parent->id;
        }

        ArticleComment::create([
            'article_id' => $article->id,
            'user_id' => Auth::id(),
            'parent_id' => $parentId,
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Komentar artikel berhasil dikirim.');
    }

    public function deleteComment(ArticleComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Komentar artikel dihapus.');
    }

    /**
     * PETUGAS: Create article
     */
    public function create()
    {
        return Inertia::render('Petugas/Articles/Create');
    }

    /**
     * PETUGAS: Store article (with pending status)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|in:artikel,berita,event',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $slug = Article::generateSlug($validated['title']);
        $validated['slug'] = $slug;
        $validated['author_id'] = Auth::id();
        $validated['status'] = 'pending';

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        Article::create($validated);

        return redirect()->route('petugas.articles.my')->with('success', 'Artikel berhasil dibuat dan menunggu persetujuan admin');
    }

    /**
     * PETUGAS: My articles
     */
    public function myArticles()
    {
        $articles = Article::where('author_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return Inertia::render('Petugas/Articles/MyArticles', [
            'articles' => $articles
        ]);
    }

    /**
     * PETUGAS: Edit own article (only if not approved)
     */
    public function edit(Article $article)
    {
        if (Auth::id() !== $article->author_id) abort(403, 'Unauthorized');
        
        if ($article->status === 'approved') {
            abort(403, 'Tidak bisa mengedit artikel yang sudah disetujui');
        }

        return Inertia::render('Petugas/Articles/Edit', [
            'article' => $article
        ]);
    }

    /**
     * PETUGAS: Update own article
     */
    public function update(Request $request, Article $article)
    {
        if (Auth::id() !== $article->author_id) abort(403, 'Unauthorized');
        
        if ($article->status === 'approved') {
            abort(403, 'Tidak bisa mengedit artikel yang sudah disetujui');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|in:artikel,berita,event',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validated['title'] !== $article->title) {
            $validated['slug'] = Article::generateSlug($validated['title']);
        }

        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                \Storage::disk('public')->delete($article->featured_image);
            }
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        $article->update($validated);

        return redirect()->route('petugas.articles.my')->with('success', 'Artikel berhasil diperbarui');
    }

    /**
     * PETUGAS: Delete own article (only if not approved)
     */
    public function destroy(Article $article)
    {
        if (Auth::id() !== $article->author_id) abort(403, 'Unauthorized');
        
        if ($article->status === 'approved') {
            abort(403, 'Tidak bisa menghapus artikel yang sudah disetujui');
        }

        if ($article->featured_image) {
            \Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        return back()->with('success', 'Artikel berhasil dihapus');
    }
}
