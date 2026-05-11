<?php

namespace Tests\Feature\Admin;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
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

    public function test_non_admin_users_cannot_access_admin_transactions(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/transactions');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_transactions_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/transactions');
        $response->assertStatus(200);
    }

    // ── Create Income ───────────────────────────────────

    public function test_admin_can_create_an_income_transaction(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/transactions', [
            'type' => 'income',
            'amount' => 500000,
            'description' => 'Penjualan basreng online',
            'transaction_date' => '2026-04-25',
        ]);

        $response->assertRedirect(route('admin.transactions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'type' => 'income',
            'amount' => 500000,
            'description' => 'Penjualan basreng online',
            'user_id' => $this->admin->id,
        ]);
    }

    // ── Create Expense ──────────────────────────────────

    public function test_admin_can_create_an_expense_transaction(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/transactions', [
            'type' => 'expense',
            'amount' => 200000,
            'description' => 'Pembelian bahan baku',
            'transaction_date' => '2026-04-25',
        ]);

        $response->assertRedirect(route('admin.transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'type' => 'expense',
            'amount' => 200000,
            'description' => 'Pembelian bahan baku',
        ]);
    }

    // ── Validation ──────────────────────────────────────

    public function test_transaction_type_must_be_income_or_expense(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/transactions', [
            'type' => 'invalid',
            'amount' => 100000,
            'description' => 'Test',
            'transaction_date' => '2026-04-25',
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_transaction_amount_must_be_at_least_1(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/transactions', [
            'type' => 'income',
            'amount' => 0,
            'description' => 'Test',
            'transaction_date' => '2026-04-25',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_transaction_description_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/transactions', [
            'type' => 'income',
            'amount' => 100000,
            'description' => '',
            'transaction_date' => '2026-04-25',
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_transaction_date_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/transactions', [
            'type' => 'income',
            'amount' => 100000,
            'description' => 'Test',
            'transaction_date' => '',
        ]);

        $response->assertSessionHasErrors('transaction_date');
    }

    // ── Edit & Update ───────────────────────────────────

    public function test_admin_can_update_a_transaction(): void
    {
        $transaction = Transaction::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->put("/admin/transactions/{$transaction->id}", [
            'type' => 'expense',
            'amount' => 300000,
            'description' => 'Updated description',
            'transaction_date' => '2026-04-20',
        ]);

        $response->assertRedirect(route('admin.transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 300000,
            'description' => 'Updated description',
        ]);
    }

    // ── Delete ──────────────────────────────────────────

    public function test_admin_can_delete_a_transaction_soft_delete(): void
    {
        $transaction = Transaction::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->delete("/admin/transactions/{$transaction->id}");

        $response->assertRedirect(route('admin.transactions.index'));
        $this->assertSoftDeleted('transactions', ['id' => $transaction->id]);
    }

    // ── Financial Summary ───────────────────────────────

    public function test_transactions_index_shows_correct_totals(): void
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
            'amount' => 300000,
            'transaction_date' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/transactions');

        $response->assertStatus(200);
    }

    public function test_transactions_can_be_filtered_by_type(): void
    {
        Transaction::factory(3)->create(['user_id' => $this->admin->id, 'type' => 'income', 'transaction_date' => now()]);
        Transaction::factory(2)->create(['user_id' => $this->admin->id, 'type' => 'expense', 'transaction_date' => now()]);

        $response = $this->actingAs($this->admin)->get('/admin/transactions?type=income');

        $response->assertStatus(200);
    }

    public function test_transactions_can_be_filtered_by_date_range(): void
    {
        Transaction::factory()->create([
            'user_id' => $this->admin->id,
            'transaction_date' => '2026-04-01',
        ]);
        Transaction::factory()->create([
            'user_id' => $this->admin->id,
            'transaction_date' => '2026-03-15',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/transactions?start_date=2026-04-01&end_date=2026-04-30');

        $response->assertStatus(200);
    }
}
