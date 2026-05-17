<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'income', 'income', 'expense']);

        $incomeProducts = [
            'Basreng Original',
            'Basreng Pedas Level 1',
            'Basreng Pedas Level 2',
            'Basreng Pedas Level 3',
            'Basreng Keju',
            'Basreng BBQ',
            'Basreng Balado',
            'Basreng Seaweed',
            'Basreng Jagung Bakar',
            'Basreng Sapi Panggang',
            'Basreng Mix Rasa',
            'Basreng Jumbo Pack',
            'Makaroni Pedas',
            'Kripik Kaca',
            'Seblak Kering'
        ];

        $expenseProducts = [
            'Plastik Kemasan 100g',
            'Plastik Kemasan 250g',
            'Minyak Goreng 2L',
            'Bumbu Balado Bubuk 1kg',
            'Bumbu Keju Bubuk 1kg',
            'Stiker Label AZ-2',
            'Gas Elpiji 3kg',
            'Lakban Bening',
            'Kardus Packing',
            'Bubble Wrap'
        ];

        return [
            'name' => fake()->unique()->randomElement($incomeProducts),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomElement([5000, 8000, 10000, 12000, 15000, 20000, 25000, 50000, 100000]),
            'image' => null, // No default image in factory
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
