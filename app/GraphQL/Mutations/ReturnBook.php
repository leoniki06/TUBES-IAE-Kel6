<?php

namespace App\GraphQL\Mutations;

use App\Models\Book;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnBook
{
    public function resolve($root, array $args)
    {
        $user = Auth::guard('api')->user();
        if ($user->role !== 'member') {
            throw new \Exception('Forbidden');
        }

        $trxId = (int) $args['transaction_id'];

        $updated = null;

        DB::transaction(function () use ($user, $trxId, &$updated) {
            $trx = Transaction::lockForUpdate()->findOrFail($trxId);

            if ($trx->user_id !== $user->id) {
                throw new \Exception('Forbidden');
            }

            if ($trx->status !== 'BORROWED') {
                throw new \Exception('Transaction is not BORROWED');
            }

            $book = Book::lockForUpdate()->findOrFail($trx->book_id);

            $returnDate = Carbon::today();
            $dueDate = Carbon::parse($trx->due_date);

            $lateDays = 0;
            $fine = 0;
            $status = 'RETURNED';

            if ($returnDate->greaterThan($dueDate)) {
                $lateDays = $dueDate->diffInDays($returnDate);
                $fine = $lateDays * 1000;
                $status = 'LATE';
            }

            $trx->update([
                'return_date' => $returnDate->toDateString(),
                'fine_amount' => $fine,
                'status' => $status,
            ]);

            $book->update([
                'stock_available' => $book->stock_available + 1,
            ]);

            $updated = $trx->fresh()->load(['book','user']);
        });

        return $updated;
    }
}
