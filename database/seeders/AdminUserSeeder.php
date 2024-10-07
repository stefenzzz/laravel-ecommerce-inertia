<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => '',
            'email' => 'admin@example.com',
            'password' => Hash::make('feadersre'),
            'role_id' => 1,
            'email_verified_at' => now(),
        ]);
    }
}
