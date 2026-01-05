<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'genre',          // ✅ PENTING
        'year',
        'stock_total',
        'stock_available',
    ];

    protected $casts = [
        'year'            => 'integer',
        'stock_total'     => 'integer',
        'stock_available' => 'integer',
    ];
}
