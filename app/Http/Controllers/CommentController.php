<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Store a new comment.
     *
     * Authenticated users can post comments on products or articles.
     * The commentable_type is provided as a simple string ('product' or 'article')
     * and resolved to the full model class name.
     */
    public function store(CommentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Resolve the commentable model and verify it exists
        $commentableClass = $request->getCommentableClass();
        $commentable = $commentableClass::findOrFail($validated['commentable_id']);

        // Create the comment
        Comment::create([
            'user_id' => $request->user()->id,
            'commentable_type' => $commentableClass,
            'commentable_id' => $commentable->id,
            'body' => $validated['body'],
            'rating' => $validated['rating'] ?? null,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
