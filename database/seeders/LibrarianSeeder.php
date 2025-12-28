<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LibrarianSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'librarian@example.com'],
            [
                'name' => 'Librarian',
                'password' => Hash::make('password123'),
                'role' => 'librarian',
            ]
        );
    }
}
