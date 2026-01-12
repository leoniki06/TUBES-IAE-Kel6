<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('search', ''));

        $perPage = (int) $request->query('per_page', 20);
        if ($perPage < 1) $perPage = 20;
        if ($perPage > 50) $perPage = 50;

        $books = Book::query()
            ->when($q !== '', function ($qq) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $qq->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                      ->orWhere('author', 'like', $like)
                      ->orWhere('isbn', 'like', $like)
                      ->orWhere('publisher', 'like', $like)
                      ->orWhere('genre', 'like', $like);
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $books,
        ]);
    }

    public function show(Book $book)
    {
        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $book,
        ]);
    }

    public function store(StoreBookRequest $request)
    {
        $payload = $request->validated();

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('books', 'public');
        }

        $book = Book::create([
            'isbn'            => $payload['isbn'] ?? null,
            'title'           => $payload['title'],
            'author'          => $payload['author'],
            'publisher'       => $payload['publisher'] ?? null,
            'genre'           => $payload['genre'],
            'year'            => $payload['year'] ?? null,
            'stock_total'     => (int) $payload['stock_total'],
            'stock_available' => (int) ($payload['stock_available'] ?? $payload['stock_total']),
            'cover'           => $coverPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Book created',
            'data'    => $book,
        ], 201);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $payload = $request->validated();

        // optional: hapus cover tanpa upload baru
        if (!empty($payload['remove_cover']) && $book->cover) {
            Storage::disk('public')->delete($book->cover);
            $book->cover = null;
        }

        // upload cover baru
        if ($request->hasFile('cover')) {
            // delete cover lama
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $book->cover = $request->file('cover')->store('books', 'public');
        }

        $book->isbn            = $payload['isbn'] ?? null;
        $book->title           = $payload['title'];
        $book->author          = $payload['author'];
        $book->publisher       = $payload['publisher'] ?? null;
        $book->genre           = $payload['genre'];
        $book->year            = $payload['year'] ?? null;
        $book->stock_total     = (int) $payload['stock_total'];
        $book->stock_available = (int) ($payload['stock_available'] ?? $payload['stock_total']);

        $book->save();

        return response()->json([
            'success' => true,
            'message' => 'Book updated',
            'data'    => $book,
        ]);
    }

    public function destroy(Book $book)
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Book deleted',
        ]);
    }
}
