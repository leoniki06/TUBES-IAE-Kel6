<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BooksSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'isbn'            => '123456789',
                'title'           => 'laskar',
                'author'          => 'orang',
                'publisher'       => 'orang',
                'genre'           => 'Psikologi',
                'year'            => 2022,
                'stock_total'     => 4,
                'stock_available' => 4,
            ],
            [
                'isbn'            => '12345678903',
                'title'           => 'pelangi',
                'author'          => 'orang',
                'publisher'       => 'orang',
                'genre'           => 'Ekonomi & Bisnis',
                'year'            => 2023,
                'stock_total'     => 3,
                'stock_available' => 3,
            ],
            [
                'isbn'            => '2345678',
                'title'           => 'laskar',
                'author'          => 'orang',
                'publisher'       => 'orang',
                'genre'           => 'Kesehatan',
                'year'            => 2023,
                'stock_total'     => 2,
                'stock_available' => 2,
            ],
            [
                'isbn'            => '1234567890',
                'title'           => 'one piece',
                'author'          => 'jepangh',
                'publisher'       => 'papa',
                'genre'           => 'Hukum & Politik',
                'year'            => 2022,
                'stock_total'     => 5,
                'stock_available' => 5,
            ],
            [
                'isbn'            => '123098765',
                'title'           => 'paseo',
                'author'          => 'kopken',
                'publisher'       => 'fore',
                'genre'           => 'Biografi',
                'year'            => 2023,
                'stock_total'     => 2,
                'stock_available' => 1,
            ],
        ];

        foreach ($books as $b) {
            // biar aman kalau dijalankan berulang kali (tidak duplikat)
            Book::updateOrCreate(
                ['isbn' => $b['isbn']],
                $b
            );
        }
    }
}
