<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('site.admin_seed.email');
        $password = config('site.admin_seed.password');

        if (! $email || ! $password || $password === 'CHANGE_ME') {
            $this->command?->warn('No admin created: set ADMIN_SEED_EMAIL and ADMIN_SEED_PASSWORD in .env, then run php artisan db:seed --class=AdminSeeder');

            return;
        }

        // Only create when missing, so re-seeding never resets a password someone changed in the admin.
        if (! AdminUser::where('email', $email)->exists()) {
            AdminUser::create(['name' => 'Administrator', 'email' => $email, 'password' => $password]);
            $this->command?->info("Admin user created: {$email}");
        }
    }
}
