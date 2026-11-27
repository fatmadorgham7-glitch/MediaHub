<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('mediahub.admin_email');
        $password = config('mediahub.admin_password');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Configure MEDIAHUB_ADMIN_EMAIL with a valid email address before seeding.');
        }

        if (! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException('Configure MEDIAHUB_ADMIN_PASSWORD with at least 12 characters before seeding.');
        }

        DB::table('users')->updateOrInsert(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
