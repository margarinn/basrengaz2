<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the admin user.
     *
     * Creates a default admin account for BasrengAz.
     * Change the password immediately in production!
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@basrengaz.com'],
            [
                'name' => 'Admin BasrengAz',
                'email' => 'admin@basrengaz.com',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: admin@basrengaz.com / password');
    }
}
