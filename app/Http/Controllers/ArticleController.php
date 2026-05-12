<?php

namespace App\Http\Controllers;

use App\Models\Article;
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
            'category' => 'required|in:artikel,berita',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'in:draft,pending,approved'
        ]);

        $slug = Article::generateSlug($validated['title']);
        $validated['slug'] = $slug;
        $validated['author_id'] = Auth::id();

        // If status is approved, set published_at to now and approved info
        if ($validated['status'] === 'approved') {
            $validated['published_at'] = now();
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
        }

        // Handle featured image
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
            'category' => 'required|in:artikel,berita',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'in:draft,pending,approved'
        ]);

        // Only regenerate slug if title changed
        if ($validated['title'] !== $article->title) {
            $validated['slug'] = Article::generateSlug($validated['title']);
        }

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                \Storage::disk('public')->delete($article->featured_image);
            }
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        // If status changed to approved
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
     * FRONTEND: Display published articles list
     */
    public function index(Request $request)
    {
        $articles = Article::published()
            ->when($request->search, function($query) use ($request) {
                return $query->where('title', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('excerpt', 'LIKE', '%' . $request->search . '%');
            })
            ->when($request->category, function($query) use ($request) {
                return $query->where('category', $request->category);
            })
            ->paginate(12);

        return Inertia::render('Frontend/Articles/Index', [
            'articles' => $articles,
            'filters' => $request->only(['search', 'category'])
        ]);
    }

    /**
     * FRONTEND: Show single article
     */
    public function show(Article $article)
    {
        // Only allow viewing approved articles
        if ($article->status !== 'approved' && (!Auth::check() || !Auth::user()->is_admin)) {
            abort(404);
        }

        $relatedArticles = Article::published()
            ->where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return Inertia::render('ArticleDetail', [
            'article' => $article->load('author'),
            'relatedArticles' => $relatedArticles
        ]);
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
            'category' => 'required|in:artikel,berita',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $slug = Article::generateSlug($validated['title']);
        $validated['slug'] = $slug;
        $validated['author_id'] = Auth::id();
        $validated['status'] = 'pending'; // Always pending for petugas

        // Handle featured image
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
            'category' => 'required|in:artikel,berita',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Only regenerate slug if title changed
        if ($validated['title'] !== $article->title) {
            $validated['slug'] = Article::generateSlug($validated['title']);
        }

        // Handle featured image
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
