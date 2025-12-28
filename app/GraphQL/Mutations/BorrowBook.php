<?php

namespace App\GraphQL\Mutations;

use App\Models\Book;
use App\Models\Transaction;
use Carbon\Carbon;
use GraphQL\Error\UserError;
use Illuminate\Support\Facades\DB;

class BorrowBook
{
    public function resolve($root, array $args)
    {
        // ambil user dari guard JWT (route graphql sudah pakai middleware auth:api)
        $user = auth('api')->user();
        if (!$user) {
            throw new UserError('Unauthorized');
        }
        if ($user->role !== 'member') {
            throw new UserError('Forbidden');
        }

        // validasi argumen
        if (!isset($args['book_id'])) {
            throw new UserError('book_id is required');
        }

        $bookId = (int) $args['book_id'];
        if ($bookId < 1) {
            throw new UserError('Invalid book_id');
        }

        // transaksi DB + return model yang pasti
        $created = DB::transaction(function () use ($user, $bookId) {

            $book = Book::query()->lockForUpdate()->find($bookId);
            if (!$book) {
                throw new UserError('Book not found');
            }

            if ((int) $book->stock_available < 1) {
                throw new UserError('Book not available');
            }

            $borrowDate = Carbon::today();
            $dueDate    = Carbon::today()->addDays(7);

            $trx = Transaction::create([
                'user_id'     => $user->id,
                'book_id'     => $book->id,
                'borrow_date' => $borrowDate->toDateString(),
                'due_date'    => $dueDate->toDateString(),
                'status'      => 'BORROWED',
                'fine_amount' => 0,
            ]);

            $book->update([
                'stock_available' => (int) $book->stock_available - 1,
            ]);

            
            return $trx->fresh()->load(['book', 'user']);
        });

        if (!$created) {

            throw new UserError('Failed to borrow book');
        }

        return $created;
    }
}
