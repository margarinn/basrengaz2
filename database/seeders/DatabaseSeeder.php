<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Always seed the admin user and company profile
        $this->call([
            AdminUserSeeder::class,
            CompanyProfileSeeder::class,
        ]);

        // Create sample regular user for testing
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@basrengaz.com',
            'phone' => '089876543210',
        ]);

        // Create sample products
        $products = Product::factory(8)->create();

        // Create sample articles (authored by admin)
        $admin = User::where('is_admin', true)->first();
        $articles = Article::factory(5)->create([
            'user_id' => $admin->id,
        ]);

        // Create sample comments on products and articles
        foreach ($products->take(4) as $product) {
            Comment::factory(3)->create([
                'user_id' => $testUser->id,
                'commentable_type' => Product::class,
                'commentable_id' => $product->id,
            ]);
        }

        foreach ($articles->take(3) as $article) {
            Comment::factory(2)->create([
                'user_id' => $testUser->id,
                'commentable_type' => Article::class,
                'commentable_id' => $article->id,
            ]);
        }

        // Create sample transactions
        // 1. Transactions for the last 6 months (Historical)
        Transaction::factory(100)->create([
            'user_id' => $admin->id,
            'transaction_date' => fn() => fake()->dateTimeBetween('-6 months', '-1 month'),
        ]);

        // 2. Transactions for this month
        Transaction::factory(50)->create([
            'user_id' => $admin->id,
            'transaction_date' => fn() => fake()->dateTimeBetween('-1 month', '-1 week'),
        ]);

        // 3. Transactions for this week
        Transaction::factory(30)->create([
            'user_id' => $admin->id,
            'transaction_date' => fn() => fake()->dateTimeBetween('-1 week', '-1 day'),
        ]);

        // 4. Transactions for today (Very recent)
        Transaction::factory(10)->create([
            'user_id' => $admin->id,
            'transaction_date' => fn() => fake()->dateTimeBetween('today', 'now'),
        ]);

        $this->command->info('Database seeded with sample data and realistic transactions!');
    }
}
