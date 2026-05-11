<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
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

    public function test_non_admin_users_cannot_access_admin_products(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/products');
        $response->assertStatus(403);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/admin/products');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_products_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/products');
        $response->assertStatus(200);
    }

    // ── Create ──────────────────────────────────────────

    public function test_admin_can_view_create_product_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/products/create');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_product(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'name' => 'Basreng Pedas Level 5',
            'description' => 'Basreng paling pedas.',
            'price' => 15000,
            'image' => UploadedFile::fake()->image('basreng.jpg'),
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Basreng Pedas Level 5',
            'price' => 15000,
        ]);

        $product = Product::first();
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_product_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'name' => '',
            'description' => 'Test',
            'price' => 10000,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_price_must_be_non_negative(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'name' => 'Test Product',
            'description' => 'Test',
            'price' => -100,
        ]);

        $response->assertSessionHasErrors('price');
    }

    // ── Edit & Update ───────────────────────────────────

    public function test_admin_can_view_edit_product_form(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->get("/admin/products/{$product->id}/edit");

        $response->assertStatus(200);
    }

    public function test_admin_can_update_a_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin)->put("/admin/products/{$product->id}", [
            'name' => 'New Name',
            'description' => 'Updated description',
            'price' => 25000,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
            'price' => 25000,
        ]);
    }

    public function test_admin_can_update_product_image(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create(['image' => null]);

        $response = $this->actingAs($this->admin)->put("/admin/products/{$product->id}", [
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => UploadedFile::fake()->image('new-image.jpg'),
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    // ── Delete ──────────────────────────────────────────

    public function test_admin_can_delete_a_product_soft_delete(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/products/{$product->id}");

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
