<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BorrowRequest;
use App\Models\Book;
use App\Models\Transaction;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $query = Transaction::query()
            ->with(['book:id,title,author,isbn', 'user:id,name,email,role'])
            ->orderByDesc('id');

        if ($user->role !== 'librarian') {
            $query->where('user_id', $user->id);
        }

        $tx = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $tx->items(),
            'meta' => [
                'current_page' => $tx->currentPage(),
                'per_page' => $tx->perPage(),
                'total' => $tx->total(),
                'last_page' => $tx->lastPage(),
            ],
        ], 200);
    }

    public function show(string $id)
    {
        $user = auth('api')->user();

        $tx = Transaction::with(['book', 'user'])->findOrFail($id);

        if ($user->role !== 'librarian' && $tx->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
                'errors'  => (object)[],
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $tx,
        ], 200);
    }

    public function borrow(BorrowRequest $request)
    {
        $user = auth('api')->user();

        $bookId = (int)$request->input('book_id');
        $days = (int)($request->input('days') ?? 7);
        if ($days < 1) $days = 7;

        return DB::transaction(function () use ($user, $bookId, $days) {
            /** @var Book $book */
            $book = Book::lockForUpdate()->findOrFail($bookId);

            if ($book->stock_available < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Book out of stock',
                    'errors'  => (object)[],
                ], 400);
            }

            $borrowDate = Carbon::today();
            $dueDate = Carbon::today()->addDays($days);

            $tx = Transaction::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'borrow_date' => $borrowDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'status' => 'BORROWED',
                'fine_amount' => 0,
            ]);

            $book->update([
                'stock_available' => $book->stock_available - 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Borrowed',
                'data' => $tx->load('book'),
            ], 201);
        });
    }

    public function return(string $id)
    {
        $user = auth('api')->user();

        return DB::transaction(function () use ($user, $id) {
            /** @var Transaction $tx */
            $tx = Transaction::lockForUpdate()->with('book')->findOrFail($id);

            if ($user->role !== 'librarian' && $tx->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                    'errors'  => (object)[],
                ], 403);
            }

            if ($tx->return_date !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already returned',
                    'errors'  => (object)[],
                ], 400);
            }

            $returnDate = Carbon::today();
            $due = Carbon::parse($tx->due_date);

            $finePerDay = 1000;
            $lateDays = max(0, $due->diffInDays($returnDate, false) * -1); // negative => late
            // Fix late calc:
            $lateDays = $returnDate->greaterThan($due) ? $due->diffInDays($returnDate) : 0;

            $fine = $lateDays * $finePerDay;
            $status = $lateDays > 0 ? 'LATE' : 'RETURNED';

            $tx->update([
                'return_date' => $returnDate->toDateString(),
                'fine_amount' => $fine,
                'status' => $status,
            ]);

            $book = $tx->book;
            $book->update([
                'stock_available' => $book->stock_available + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Returned',
                'data' => $tx->fresh()->load('book'),
            ], 200);
        });
    }
}
