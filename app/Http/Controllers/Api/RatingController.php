<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * List approved ratings/comments (public).
     */
    public function index(Request $request): JsonResponse
    {
        $comments = Comment::approved()
            ->withRating()
            ->with('user:id,name')
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $comments->through(fn ($c) => [
                'id' => $c->id,
                'name' => $c->user?->name ?? 'Anonim',
                'rating' => $c->rating,
                'review' => $c->body,
                'avatar' => '',
                'created_at' => $c->created_at?->toISOString(),
                'updated_at' => $c->updated_at?->toISOString(),
            ])->items(),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Store a new rating/comment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|max:1000',
            'commentable_type' => 'sometimes|string|in:product,article',
            'commentable_id' => 'sometimes|integer|min:1',
        ]);

        // Default to first product if no commentable specified
        $commentableType = Product::class;
        $commentableId = 1;

        if (isset($validated['commentable_type'])) {
            $commentableType = match ($validated['commentable_type']) {
                'product' => Product::class,
                'article' => \App\Models\Article::class,
            };
            $commentableId = $validated['commentable_id'] ?? 1;
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'body' => $validated['review'],
            'rating' => $validated['rating'],
            'is_approved' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $comment->id,
                'name' => $request->user()->name,
                'rating' => $comment->rating,
                'review' => $comment->body,
            ],
            'message' => 'Ulasan berhasil ditambahkan.',
        ], 201);
    }

    /**
     * Update a rating/comment.
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|max:1000',
        ]);

        $comment->update([
            'body' => $validated['review'],
            'rating' => $validated['rating'],
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $comment->id,
                'name' => $comment->user?->name,
                'rating' => $comment->rating,
                'review' => $comment->body,
            ],
            'message' => 'Ulasan berhasil diperbarui.',
        ]);
    }

    /**
     * Delete a rating/comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dihapus.',
        ]);
    }
}
