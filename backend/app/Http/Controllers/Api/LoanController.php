<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    // GET /api/loans
    // Member: hanya lihat miliknya
    // Librarian: bisa lihat semua (filter status/search)
    public function index(Request $request)
    {
        $user = $request->user();
        $q = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        $loans = Loan::query()
            ->with(['book:id,title,author', 'member:id,name,email', 'librarian:id,name,email'])
            ->when($user->role === 'member', fn($qr) => $qr->where('member_id', $user->id))
            ->when($status !== '', fn($qr) => $qr->where('status', $status))
            ->when($q !== '', function ($qr) use ($q) {
                $qr->whereHas('book', function ($b) use ($q) {
                    $b->where('title', 'like', "%{$q}%")
                      ->orWhere('author', 'like', "%{$q}%");
                })->orWhereHas('member', function ($m) use ($q) {
                    $m->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $loans,
        ]);
    }

    // POST /api/loans (member request)
    public function store(StoreLoanRequest $request)
    {
        $user = $request->user();
        if ($user->role !== 'member') {
            return response()->json(['success' => false, 'message' => 'Only member can request loans.'], 403);
        }

        $qty = (int) ($request->input('qty', 1));
        $bookId = (int) $request->input('book_id');

        $loan = DB::transaction(function () use ($user, $bookId, $qty, $request) {
            /** @var Book $book */
            $book = Book::lockForUpdate()->findOrFail($bookId);

            // asumsi book punya kolom stock / available_qty.
            // Kalau kolommu beda, ganti di sini.
            $available = (int) ($book->stock ?? 0);

            if ($available < $qty) {
                abort(422, 'Stock not enough.');
            }

            // Optional: jangan kurangi stok saat requested.
            // Lebih aman kurangi stok saat BORROWED (checkout).
            // Jadi di sini tidak mengurangi stok.

            return Loan::create([
                'member_id'    => $user->id,
                'book_id'      => $book->id,
                'status'       => 'requested',
                'qty'          => $qty,
                'requested_at' => Carbon::now()->toDateString(),
                'note'         => $request->input('note'),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Loan requested.',
            'data' => $loan->load(['book:id,title,author', 'member:id,name,email']),
        ], 201);
    }

    // GET /api/loans/{id}
    public function show(Request $request, int $id)
    {
        $user = $request->user();

        $loan = Loan::with(['book', 'member', 'librarian'])->findOrFail($id);

        if ($user->role === 'member' && $loan->member_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $loan]);
    }

    // PATCH /api/loans/{id}/approve (librarian)
    public function approve(Request $request, int $id)
    {
        $user = $request->user();
        if ($user->role !== 'librarian') {
            return response()->json(['success' => false, 'message' => 'Only librarian can approve.'], 403);
        }

        $loan = Loan::findOrFail($id);
        if ($loan->status !== 'requested') {
            return response()->json(['success' => false, 'message' => 'Invalid status transition.'], 422);
        }

        $loan->update([
            'status'      => 'approved',
            'processed_by'=> $user->id,
            'approved_at' => Carbon::now()->toDateString(),
        ]);

        return response()->json(['success' => true, 'message' => 'Approved', 'data' => $loan->load(['book','member','librarian'])]);
    }

    // PATCH /api/loans/{id}/reject (librarian)
    public function reject(Request $request, int $id)
    {
        $user = $request->user();
        if ($user->role !== 'librarian') {
            return response()->json(['success' => false, 'message' => 'Only librarian can reject.'], 403);
        }

        $loan = Loan::findOrFail($id);
        if (!in_array($loan->status, ['requested','approved'], true)) {
            return response()->json(['success' => false, 'message' => 'Invalid status transition.'], 422);
        }

        $loan->update([
            'status'       => 'rejected',
            'processed_by' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Rejected', 'data' => $loan->load(['book','member','librarian'])]);
    }

    // PATCH /api/loans/{id}/checkout (librarian) => status borrowed + kurangi stok
    public function checkout(Request $request, int $id)
    {
        $user = $request->user();
        if ($user->role !== 'librarian') {
            return response()->json(['success' => false, 'message' => 'Only librarian can checkout.'], 403);
        }

        $loan = Loan::findOrFail($id);
        if ($loan->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved loan can be checked out.'], 422);
        }

        $dueDays = (int) ($request->input('due_days', 7));
        $dueDays = max(1, min(60, $dueDays));

        DB::transaction(function () use ($loan, $user, $dueDays) {
            $book = Book::lockForUpdate()->findOrFail($loan->book_id);
            $available = (int) ($book->stock ?? 0);
            if ($available < (int)$loan->qty) {
                abort(422, 'Stock not enough for checkout.');
            }

            // kurangi stok saat buku benar2 dipinjam
            $book->stock = $available - (int)$loan->qty;
            $book->save();

            $today = Carbon::now();
            $loan->status = 'borrowed';
            $loan->processed_by = $user->id;
            $loan->borrowed_at = $today->toDateString();
            $loan->due_at = $today->copy()->addDays($dueDays)->toDateString();
            $loan->save();
        });

        return response()->json(['success' => true, 'message' => 'Checked out', 'data' => $loan->fresh()->load(['book','member','librarian'])]);
    }

    // PATCH /api/loans/{id}/return (librarian) => status returned + tambah stok
    public function returnBook(Request $request, int $id)
    {
        $user = $request->user();
        if ($user->role !== 'librarian') {
            return response()->json(['success' => false, 'message' => 'Only librarian can return.'], 403);
        }

        $loan = Loan::findOrFail($id);
        if ($loan->status !== 'borrowed') {
            return response()->json(['success' => false, 'message' => 'Only borrowed loan can be returned.'], 422);
        }

        DB::transaction(function () use ($loan, $user) {
            $book = Book::lockForUpdate()->findOrFail($loan->book_id);

            $book->stock = (int)($book->stock ?? 0) + (int)$loan->qty;
            $book->save();

            $loan->status = 'returned';
            $loan->processed_by = $user->id;
            $loan->returned_at = Carbon::now()->toDateString();
            $loan->save();
        });

        return response()->json(['success' => true, 'message' => 'Returned', 'data' => $loan->fresh()->load(['book','member','librarian'])]);
    }

    // PATCH /api/loans/{id}/cancel (member bisa cancel saat requested)
    public function cancel(Request $request, int $id)
    {
        $user = $request->user();
        $loan = Loan::findOrFail($id);

        if ($user->role === 'member') {
            if ($loan->member_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            if ($loan->status !== 'requested') {
                return response()->json(['success' => false, 'message' => 'Only requested loan can be cancelled by member.'], 422);
            }
        } else {
            // librarian boleh cancel sebelum borrowed (opsional)
            if (!in_array($loan->status, ['requested','approved'], true)) {
                return response()->json(['success' => false, 'message' => 'Invalid status transition.'], 422);
            }
            $loan->processed_by = $user->id;
        }

        $loan->status = 'cancelled';
        $loan->save();

        return response()->json(['success' => true, 'message' => 'Cancelled', 'data' => $loan->fresh()->load(['book','member','librarian'])]);
    }
}
