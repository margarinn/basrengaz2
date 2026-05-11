<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of active products (public).
     */
    public function index(): Response
    {
        $products = Product::active()
            ->ordered()
            ->paginate(12)
            ->through(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'image_url' => $product->image_url,
                'average_rating' => $product->average_rating,
                'rating_count' => $product->rating_count,
            ]);

        return Inertia::render('Products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Display a single product with comments and ratings (public).
     */
    public function show(Product $product): Response
    {
        // Ensure the product is active (not hidden)
        if (!$product->is_active) {
            abort(404);
        }

        $comments = $product->approvedComments()
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

        return Inertia::render('Products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'image_url' => $product->image_url,
                'average_rating' => $product->average_rating,
                'rating_count' => $product->rating_count,
            ],
            'comments' => $comments,
        ]);
    }
}
