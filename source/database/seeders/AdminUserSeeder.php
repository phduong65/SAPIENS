<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sapienshouse.vn'],
            [
                'name' => 'Sapiens Admin',
                'email' => 'admin@sapienshouse.vn',
                'password' => Hash::make('sapiens2024!'),
                'email_verified_at' => now(),
            ]
        );
    }
}
