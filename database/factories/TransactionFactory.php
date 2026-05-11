<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense']);

        $incomeDescriptions = [
            'Penjualan basreng online',
            'Penjualan basreng offline',
            'Penjualan via marketplace',
            'Penjualan grosir',
            'Penjualan event/bazaar',
            'Pendapatan reseller',
        ];

        $expenseDescriptions = [
            'Pembelian bahan baku',
            'Biaya kemasan/packaging',
            'Biaya pengiriman',
            'Biaya listrik',
            'Biaya gas',
            'Gaji karyawan',
            'Biaya sewa tempat',
            'Biaya pemasaran',
            'Pembelian peralatan',
            'Biaya operasional lainnya',
        ];

        return [
            'user_id' => User::factory(),
            'type' => $type,
            'amount' => $type === 'income'
                ? fake()->randomElement([50000, 100000, 150000, 200000, 300000, 500000, 750000, 1000000])
                : fake()->randomElement([25000, 50000, 75000, 100000, 150000, 200000, 300000, 500000]),
            'description' => $type === 'income'
                ? fake()->randomElement($incomeDescriptions)
                : fake()->randomElement($expenseDescriptions),
            'transaction_date' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Create an income transaction.
     */
    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
        ]);
    }

    /**
     * Create an expense transaction.
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
        ]);
    }
}
