<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * List published articles (public).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::latestPublished();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = $query->paginate($request->integer('per_page', 9));

        return response()->json([
            'success' => true,
            'data' => $articles->through(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'slug' => $a->slug,
                'description' => $a->excerpt,
                'image' => $a->image_url,
                'published_at' => $a->published_at?->format('d M Y'),
                'author' => $a->author?->name,
                'created_at' => $a->created_at?->toISOString(),
                'updated_at' => $a->updated_at?->toISOString(),
            ])->items(),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Show a single article (public).
     */
    public function show(int $id): JsonResponse
    {
        $article = Article::with('author:id,name')->findOrFail($id);

        if (!$article->is_published) {
            return response()->json(['success' => false, 'message' => 'Artikel tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'body' => $article->body,
                'description' => $article->excerpt,
                'image' => $article->image_url,
                'published_at' => $article->published_at?->format('d M Y'),
                'author' => $article->author?->name,
            ],
        ]);
    }

    /**
     * Store a new article (admin).
     */
    public function store(ArticleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $article->id,
                'title' => $article->title,
                'description' => $article->excerpt,
                'image' => $article->image_url,
            ],
            'message' => 'Artikel berhasil ditambahkan.',
        ], 201);
    }

    /**
     * Update an article (admin). Uses POST + _method for multipart.
     */
    public function update(ArticleRequest $request, int $id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $article->id,
                'title' => $article->title,
                'description' => $article->excerpt,
                'image' => $article->image_url,
            ],
            'message' => 'Artikel berhasil diperbarui.',
        ]);
    }

    /**
     * Delete an article (admin, soft delete).
     */
    public function destroy(int $id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Artikel berhasil dihapus.',
        ]);
    }
}
