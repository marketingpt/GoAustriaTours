<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('site.admin_seed.name') ?? 'Administrator';
        $email = config('site.admin_seed.email');
        $password = config('site.admin_seed.password');

        if (! $email || ! $password || $password === 'CHANGE_ME') {
            $this->command?->warn('No admin created: set ADMIN_SEED_EMAIL and ADMIN_SEED_PASSWORD in .env, then run php artisan db:seed --class=AdminSeeder');

            return;
        }

        $admin = AdminUser::where('email', $email)->first();

        if ($admin) {
            $admin->forceFill(['name' => $name])->save();
            $this->command?->info("Admin user updated: {$email}");

            return;
        }

        AdminUser::create(['name' => $name, 'email' => $email, 'password' => $password]);
        $this->command?->info("Admin user created: {$email}");
    }
}
