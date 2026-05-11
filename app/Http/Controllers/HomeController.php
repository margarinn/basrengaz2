<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CompanyProfile;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the landing page.
     *
     * Shows company profile, featured products, and latest articles.
     */
    public function index(): Response
    {
        $profile = CompanyProfile::getInstance();

        $products = Product::active()
            ->ordered()
            ->take(6)
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'image_url' => $product->image_url,
                'average_rating' => $product->average_rating,
                'rating_count' => $product->rating_count,
            ]);

        $articles = Article::latestPublished()
            ->take(3)
            ->get()
            ->map(fn ($article) => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'image_url' => $article->image_url,
                'published_at' => $article->published_at?->format('d M Y'),
                'author' => $article->author?->name,
            ]);

        return Inertia::render('Home', [
            'profile' => [
                'name' => $profile->name,
                'description' => $profile->description,
                'address' => $profile->address,
                'map_embed_url' => $profile->map_embed_url,
                'phone' => $profile->phone,
                'email' => $profile->email,
                'whatsapp' => $profile->whatsapp,
                'whatsapp_link' => $profile->whatsapp_link,
                'instagram' => $profile->instagram,
                'instagram_url' => $profile->instagram_url,
                'facebook' => $profile->facebook,
                'tiktok' => $profile->tiktok,
                'logo_url' => $profile->logo_url,
                'hero_image_url' => $profile->hero_image_url,
            ],
            'featuredProducts' => $products,
            'latestArticles' => $articles,
        ]);
    }
}
