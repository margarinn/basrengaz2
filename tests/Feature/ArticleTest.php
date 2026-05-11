<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_view_published_articles_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Article::factory(2)->create(['user_id' => $admin->id, 'is_published' => true, 'published_at' => now()]);
        Article::factory(1)->create(['user_id' => $admin->id, 'is_published' => false]);

        $response = $this->get('/articles');

        $response->assertStatus(200);
    }

    public function test_anyone_can_view_a_single_published_article_by_slug(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $article = Article::factory()->create([
            'user_id' => $admin->id,
            'is_published' => true,
            'published_at' => now(),
            'title' => 'Basreng Terbaru 2026',
        ]);

        $response = $this->get("/articles/{$article->slug}");

        $response->assertStatus(200);
    }

    public function test_unpublished_articles_return_404(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $article = Article::factory()->create([
            'user_id' => $admin->id,
            'is_published' => false,
        ]);

        $response = $this->get("/articles/{$article->slug}");

        $response->assertStatus(404);
    }

    public function test_articles_show_comments(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $admin->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $article->comments()->create([
            'user_id' => $user->id,
            'body' => 'Artikel yang informatif!',
            'rating' => 4,
            'is_approved' => true,
        ]);

        $response = $this->get("/articles/{$article->slug}");

        $response->assertStatus(200);
    }
}
