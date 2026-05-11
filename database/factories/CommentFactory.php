<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $positiveComments = [
            'Enak banget! Recommended!',
            'Rasa basrengnya mantap, pedasnya pas.',
            'Packaging rapi, pengiriman cepat.',
            'Sudah langganan, selalu enak.',
            'Harga terjangkau, rasa premium.',
            'Cocok buat camilan nonton.',
            'Anak-anak di rumah suka banget.',
            'Renyah dan gurih, bikin nagih!',
            'Porsinya pas, harganya worth it.',
            'Varian rasanya banyak, semua enak.',
        ];

        return [
            'user_id' => User::factory(),
            'commentable_type' => Product::class,
            'commentable_id' => Product::factory(),
            'body' => fake()->randomElement($positiveComments),
            'rating' => fake()->numberBetween(3, 5), // Mostly positive ratings
            'is_approved' => true,
        ];
    }

    /**
     * Indicate that the comment has no rating.
     */
    public function withoutRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => null,
        ]);
    }

    /**
     * Indicate that the comment is not approved.
     */
    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }
}
