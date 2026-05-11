<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_view_products_list(): void
    {
        Product::factory(3)->create();

        $response = $this->get('/products');

        $response->assertStatus(200);
    }

    public function test_anyone_can_view_a_single_active_product(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $response = $this->get("/products/{$product->id}");

        $response->assertStatus(200);
    }

    public function test_inactive_products_return_404(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $response = $this->get("/products/{$product->id}");

        $response->assertStatus(404);
    }

    public function test_products_show_comments_and_ratings(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $product->comments()->create([
            'user_id' => $user->id,
            'body' => 'Enak banget!',
            'rating' => 5,
            'is_approved' => true,
        ]);

        $response = $this->get("/products/{$product->id}");

        $response->assertStatus(200);
    }
}
