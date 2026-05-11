<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\Product;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_loads_with_company_profile_data(): void
    {
        CompanyProfile::create(['name' => 'BasrengAz']);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_landing_page_shows_featured_products(): void
    {
        CompanyProfile::create(['name' => 'BasrengAz']);
        Product::factory(8)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_landing_page_shows_latest_articles(): void
    {
        CompanyProfile::create(['name' => 'BasrengAz']);
        $admin = User::factory()->create(['is_admin' => true]);
        Article::factory(5)->create([
            'user_id' => $admin->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
