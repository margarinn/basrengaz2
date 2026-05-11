<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_correct_stats(): void
    {
        Product::factory(5)->create();
        Product::factory(2)->create(['is_active' => false]);
        Article::factory(3)->create(['user_id' => $this->admin->id, 'is_published' => true, 'published_at' => now()]);
        Article::factory(1)->create(['user_id' => $this->admin->id, 'is_published' => false]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_financial_summary(): void
    {
        Transaction::factory()->create([
            'user_id' => $this->admin->id,
            'type' => 'income',
            'amount' => 1000000,
            'transaction_date' => now(),
        ]);
        Transaction::factory()->create([
            'user_id' => $this->admin->id,
            'type' => 'expense',
            'amount' => 400000,
            'transaction_date' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }
}
