<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of all articles (including unpublished).
     */
    public function index(): Response
    {
        $articles = Article::with('author:id,name')
            ->latest()
            ->paginate(15)
            ->through(fn ($article) => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'image_url' => $article->image_url,
                'is_published' => $article->is_published,
                'published_at' => $article->published_at?->format('d M Y'),
                'author' => $article->author?->name,
                'created_at' => $article->created_at->format('d M Y'),
            ]);

        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles,
        ]);
    }

    /**
     * Show the form for creating a new article.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Articles/Create');
    }

    /**
     * Store a newly created article.
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Set the author to the current admin user
        $validated['user_id'] = $request->user()->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        Article::create($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil ditambahkan.');
    }

    /**
     * Show the form for editing an article.
     * Note: In admin, we find by ID not by slug.
     */
    public function edit(int $id): Response
    {
        $article = Article::findOrFail($id);

        return Inertia::render('Admin/Articles/Edit', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'body' => $article->body,
                'image' => $article->image,
                'image_url' => $article->image_url,
                'is_published' => $article->is_published,
                'published_at' => $article->published_at?->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Update the specified article.
     */
    public function update(ArticleRequest $request, int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);
        $validated = $request->validated();

        // Handle image upload (replace old image if new one is provided)
        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Soft delete the specified article.
     */
    public function destroy(int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);
        $article->delete(); // Soft delete

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }
}
