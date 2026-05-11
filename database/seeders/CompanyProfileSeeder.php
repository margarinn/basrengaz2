<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    /**
     * Seed the company profile with initial BasrengAz data.
     *
     * All values are placeholders — update via admin dashboard.
     */
    public function run(): void
    {
        CompanyProfile::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'BasrengAz',
                'description' => 'BasrengAz adalah usaha yang bergerak di bidang kuliner, khususnya basreng (bakso goreng) dengan berbagai varian rasa yang lezat dan berkualitas.',
                'address' => 'Jl. Contoh No. 123, Kota, Provinsi',
                'latitude' => null,
                'longitude' => null,
                'map_embed_url' => null,
                'phone' => '08xxxxxxxxxx',
                'email' => 'info@basrengaz.com',
                'whatsapp' => '628xxxxxxxxxx',
                'instagram' => '@basrengaz',
                'facebook' => null,
                'tiktok' => '@basrengaz',
                'logo' => null,
                'hero_image' => null,
            ]
        );

        $this->command->info('Company profile seeded for BasrengAz.');
    }
}
