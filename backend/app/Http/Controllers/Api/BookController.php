<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;

use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $books = Book::query()
            ->when($q, function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('author', 'like', "%{$q}%")
                      ->orWhere('isbn', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $books->items(),
            'meta' => [
                'current_page' => $books->currentPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
                'last_page' => $books->lastPage(),
            ],
        ], 200);
    }

    public function store(StoreBookRequest $request)
    {
        $book = Book::create([
            'isbn' => $request->input('isbn'),
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'publisher' => $request->input('publisher'),
            'year' => $request->input('year'),
            'stock_total' => (int)$request->input('stock_total'),
            'stock_available' => (int)$request->input('stock_total'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Created',
            'data' => $book,
        ], 201);
    }

    public function show(string $id)
    {
        $book = Book::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $book,
        ], 200);
    }

    public function update(UpdateBookRequest $request, string $id)
    {
        $book = Book::findOrFail($id);

        $newTotal = (int)$request->input('stock_total');
        $oldTotal = (int)$book->stock_total;
        $oldAvail = (int)$book->stock_available;

        $delta = $newTotal - $oldTotal;
        $newAvail = $oldAvail + $delta;
        if ($newAvail < 0) $newAvail = 0;

        $book->update([
            'isbn' => $request->input('isbn'),
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'publisher' => $request->input('publisher'),
            'year' => $request->input('year'),
            'stock_total' => $newTotal,
            'stock_available' => $newAvail,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $book->fresh(),
        ], 200);
    }

    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted',
            'data' => (object)[],
        ], 200);
    }
}
