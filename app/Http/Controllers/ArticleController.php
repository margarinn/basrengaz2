<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of published articles (public).
     */
    public function index(): Response
    {
        $articles = Article::latestPublished()
            ->paginate(9)
            ->through(fn ($article) => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'image_url' => $article->image_url,
                'published_at' => $article->published_at?->format('d M Y'),
                'author' => $article->author?->name,
            ]);

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
        ]);
    }

    /**
     * Display a single article with comments (public).
     * Uses slug for SEO-friendly URLs.
     */
    public function show(Article $article): Response
    {
        // Ensure the article is published
        if (!$article->is_published) {
            abort(404);
        }

        $comments = $article->approvedComments()
            ->with('user:id,name')
            ->latest()
            ->paginate(10)
            ->through(fn ($comment) => [
                'id' => $comment->id,
                'body' => $comment->body,
                'rating' => $comment->rating,
                'user_name' => $comment->user->name,
                'created_at' => $comment->created_at->diffForHumans(),
            ]);

        return Inertia::render('Articles/Show', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'body' => $article->body,
                'image_url' => $article->image_url,
                'published_at' => $article->published_at?->format('d M Y'),
                'author' => $article->author?->name,
            ],
            'comments' => $comments,
        ]);
    }
}
