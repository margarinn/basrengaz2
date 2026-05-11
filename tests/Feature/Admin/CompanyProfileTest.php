<?php

namespace Tests\Feature\Admin;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
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

    public function test_non_admin_users_cannot_access_company_profile_management(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/company-profile');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_company_profile_edit_page(): void
    {
        CompanyProfile::create(['name' => 'BasrengAz']);

        $response = $this->actingAs($this->admin)->get('/admin/company-profile');

        $response->assertStatus(200);
    }

    // ── Update ──────────────────────────────────────────

    public function test_admin_can_update_company_profile(): void
    {
        CompanyProfile::create(['name' => 'BasrengAz']);

        $response = $this->actingAs($this->admin)->put('/admin/company-profile', [
            'name' => 'BasrengAz Premium',
            'description' => 'Basreng terenak se-Indonesia.',
            'address' => 'Jl. Basreng No. 1, Bandung',
            'phone' => '08123456789',
            'email' => 'info@basrengaz.com',
            'whatsapp' => '6281234567890',
            'instagram' => '@basrengaz',
        ]);

        $response->assertRedirect(route('admin.company-profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('company_profiles', [
            'name' => 'BasrengAz Premium',
            'address' => 'Jl. Basreng No. 1, Bandung',
            'whatsapp' => '6281234567890',
        ]);
    }

    public function test_admin_can_update_company_logo(): void
    {
        Storage::fake('public');
        CompanyProfile::create(['name' => 'BasrengAz']);

        $response = $this->actingAs($this->admin)->put('/admin/company-profile', [
            'name' => 'BasrengAz',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ]);

        $response->assertRedirect(route('admin.company-profile.edit'));

        $profile = CompanyProfile::first();
        $this->assertNotNull($profile->logo);
        Storage::disk('public')->assertExists($profile->logo);
    }

    public function test_admin_can_update_hero_image(): void
    {
        Storage::fake('public');
        CompanyProfile::create(['name' => 'BasrengAz']);

        $response = $this->actingAs($this->admin)->put('/admin/company-profile', [
            'name' => 'BasrengAz',
            'hero_image' => UploadedFile::fake()->image('hero.jpg'),
        ]);

        $response->assertRedirect(route('admin.company-profile.edit'));

        $profile = CompanyProfile::first();
        $this->assertNotNull($profile->hero_image);
        Storage::disk('public')->assertExists($profile->hero_image);
    }

    public function test_admin_can_update_google_maps_embed_url(): void
    {
        CompanyProfile::create(['name' => 'BasrengAz']);

        $response = $this->actingAs($this->admin)->put('/admin/company-profile', [
            'name' => 'BasrengAz',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=test',
            'latitude' => -6.9147444,
            'longitude' => 107.6098111,
        ]);

        $response->assertRedirect(route('admin.company-profile.edit'));
        $this->assertDatabaseHas('company_profiles', [
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=test',
        ]);
    }

    // ── Validation ──────────────────────────────────────

    public function test_company_name_is_required(): void
    {
        CompanyProfile::create(['name' => 'BasrengAz']);

        $response = $this->actingAs($this->admin)->put('/admin/company-profile', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
