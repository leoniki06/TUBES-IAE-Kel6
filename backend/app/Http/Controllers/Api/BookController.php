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
        $q = trim((string) $request->query('search', ''));

        $books = Book::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                        ->orWhere('author', 'like', "%{$q}%")
                        ->orWhere('isbn', 'like', "%{$q}%")
                        ->orWhere('publisher', 'like', "%{$q}%")
                        ->orWhere('genre', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $books,
        ]);
    }

    public function store(StoreBookRequest $request)
    {
        $total = (int) $request->input('stock_total', 0);

        /**
         * ✅ kalau user isi stock_available, pakai itu
         * kalau tidak dikirim, default = total (biar masih masuk akal)
         */
        $avail = $request->has('stock_available')
            ? (int) $request->input('stock_available', 0)
            : $total;

        // ✅ hard guard (double safety, walau seharusnya juga divalidasi di FormRequest)
        if ($avail > $total) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => [
                    'stock_available' => ['Stock Available tidak boleh lebih besar dari Stock Total.'],
                ],
            ], 422);
        }

        $book = Book::create([
            'isbn'            => $request->input('isbn'),
            'title'           => $request->input('title'),
            'author'          => $request->input('author'),
            'publisher'       => $request->input('publisher'),
            'genre'           => $request->input('genre'),
            'year'            => $request->input('year'),
            'stock_total'     => $total,
            'stock_available' => $avail,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Created',
            'data'    => $book,
        ], 201);
    }

    public function show(string $id)
    {
        $book = Book::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $book,
        ]);
    }

    public function update(UpdateBookRequest $request, string $id)
    {
        $book = Book::findOrFail($id);

        // total wajib ada di request (sesuai form kamu)
        $total = (int) $request->input('stock_total', (int) $book->stock_total);

        /**
         * ✅ pakai stock_available dari request kalau ada,
         * kalau tidak ada, pertahankan existing (bukan delta)
         */
        $avail = $request->has('stock_available')
            ? (int) $request->input('stock_available', (int) $book->stock_available)
            : (int) $book->stock_available;

        // clamp basic
        if ($total < 0) $total = 0;
        if ($avail < 0) $avail = 0;

        // ✅ rule utama
        if ($avail > $total) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => [
                    'stock_available' => ['Stock Available tidak boleh lebih besar dari Stock Total.'],
                ],
            ], 422);
        }

        $book->update([
            'isbn'            => $request->input('isbn'),
            'title'           => $request->input('title'),
            'author'          => $request->input('author'),
            'publisher'       => $request->input('publisher'),
            'genre'           => $request->input('genre'),
            'year'            => $request->input('year'),
            'stock_total'     => $total,
            'stock_available' => $avail,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data'    => $book->fresh(),
        ]);
    }

    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted',
            'data'    => (object) [],
        ]);
    }
}
