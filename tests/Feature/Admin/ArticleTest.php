<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->user = User::factory()->create(['is_admin' => false]);
    }

    // ── Access Control ──────────────────────────────────

    public function test_non_admin_users_cannot_access_admin_articles(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/articles');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_articles_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/articles');
        $response->assertStatus(200);
    }

    // ── Create ──────────────────────────────────────────

    public function test_admin_can_create_an_article(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'title' => 'Basreng Az Buka Cabang Baru',
            'body' => 'Kabar gembira, BasrengAz membuka cabang baru di Bandung.',
            'image' => UploadedFile::fake()->image('article.jpg'),
            'is_published' => true,
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('articles', [
            'title' => 'Basreng Az Buka Cabang Baru',
            'user_id' => $this->admin->id,
            'is_published' => true,
        ]);

        // Verify slug was auto-generated
        $article = Article::first();
        $this->assertNotEmpty($article->slug);
        $this->assertStringContainsString('basreng-az-buka-cabang-baru', $article->slug);
    }

    public function test_article_title_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'title' => '',
            'body' => 'Content here',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_article_body_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'title' => 'Test Title',
            'body' => '',
        ]);

        $response->assertSessionHasErrors('body');
    }

    // ── Edit & Update ───────────────────────────────────

    public function test_admin_can_update_an_article(): void
    {
        $article = Article::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->put("/admin/articles/{$article->id}", [
            'title' => 'Updated Title',
            'body' => 'Updated content.',
            'is_published' => true,
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_admin_can_update_article_image(): void
    {
        Storage::fake('public');
        $article = Article::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->put("/admin/articles/{$article->id}", [
            'title' => $article->title,
            'body' => $article->body,
            'image' => UploadedFile::fake()->image('new-article-img.jpg'),
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        $article->refresh();
        $this->assertNotNull($article->image);
    }

    // ── Delete ──────────────────────────────────────────

    public function test_admin_can_delete_an_article_soft_delete(): void
    {
        $article = Article::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->delete("/admin/articles/{$article->id}");

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }
}
