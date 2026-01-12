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
        'genre',
        'year',
        'stock_total',
        'stock_available',
        'cover', // ✅ tambah
    ];

    protected $casts = [
        'year'            => 'integer',
        'stock_total'     => 'integer',
        'stock_available' => 'integer',
    ];

    // Optional helper: URL cover siap pakai
    protected $appends = ['cover_url'];

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover) return null;
        return asset('storage/' . ltrim($this->cover, '/'));
    }
}
