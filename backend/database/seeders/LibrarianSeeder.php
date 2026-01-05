<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LibrarianSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@library.com'],
            [
                'name' => 'Admin Librarian',
                'password' => Hash::make('12345678'),
                'role' => 'librarian',
                'is_active' => true,
            ]
        );
    }
}
