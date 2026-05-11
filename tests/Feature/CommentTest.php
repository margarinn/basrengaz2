<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Product;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_post_comments(): void
    {
        $product = Product::factory()->create();

        $response = $this->post('/comments', [
            'commentable_type' => 'product',
            'commentable_id' => $product->id,
            'body' => 'Test comment',
            'rating' => 5,
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_comment_on_products(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post('/comments', [
            'commentable_type' => 'product',
            'commentable_id' => $product->id,
            'body' => 'Basrengnya enak banget!',
            'rating' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_type' => Product::class,
            'commentable_id' => $product->id,
            'body' => 'Basrengnya enak banget!',
            'rating' => 5,
        ]);
    }

    public function test_authenticated_users_can_comment_on_articles(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $article = Article::factory()->create(['user_id' => $admin->id]);

        $response = $this->actingAs($user)->post('/comments', [
            'commentable_type' => 'article',
            'commentable_id' => $article->id,
            'body' => 'Artikel yang bagus!',
            'rating' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_type' => Article::class,
            'commentable_id' => $article->id,
            'body' => 'Artikel yang bagus!',
            'rating' => 4,
        ]);
    }

    public function test_comments_can_be_posted_without_rating(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post('/comments', [
            'commentable_type' => 'product',
            'commentable_id' => $product->id,
            'body' => 'Komentar tanpa rating',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'body' => 'Komentar tanpa rating',
            'rating' => null,
        ]);
    }

    public function test_comment_body_is_required(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post('/comments', [
            'commentable_type' => 'product',
            'commentable_id' => $product->id,
            'body' => '',
        ]);

        $response->assertSessionHasErrors('body');
    }

    public function test_rating_must_be_between_1_and_5(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post('/comments', [
            'commentable_type' => 'product',
            'commentable_id' => $product->id,
            'body' => 'Test',
            'rating' => 6,
        ]);

        $response->assertSessionHasErrors('rating');
    }

    public function test_invalid_commentable_type_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/comments', [
            'commentable_type' => 'invalid',
            'commentable_id' => 1,
            'body' => 'Test',
        ]);

        $response->assertSessionHasErrors('commentable_type');
    }
}
