<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table      = 'transactions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id','book_id','borrow_date','due_date','return_date','fine_amount','status'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDashboardSummary(int $userId): array
    {
        // total buku sedang dipinjam
        $activeBorrowed = $this->where('user_id', $userId)
            ->groupStart()
                ->where('return_date', null)
                ->orWhere('return_date', '')
            ->groupEnd()
            ->countAllResults();

        // jatuh tempo terdekat
        $nearestDue = $this->select('due_date')
            ->where('user_id', $userId)
            ->groupStart()
                ->where('return_date', null)
                ->orWhere('return_date', '')
            ->groupEnd()
            ->orderBy('due_date', 'ASC')
            ->first();

        // total denda aktif
        $fine = $this->selectSum('fine_amount')
            ->where('user_id', $userId)
            ->where('fine_amount >', 0)
            ->get()
            ->getRow()
            ->fine_amount ?? 0;

        // total transaksi
        $totalTransactions = $this->where('user_id', $userId)->countAllResults();

        return [
            'active_borrowed' => (int)$activeBorrowed,
            'nearest_due'     => $nearestDue['due_date'] ?? null,
            'fine_amount'     => (int)$fine,
            'total_trx'       => (int)$totalTransactions,
        ];
    }

    public function getActiveBorrowed(int $userId): array
    {
        // Ambil transaksi aktif user + join buku
        return $this->select('transactions.*, books.title, books.author, books.cover')
            ->join('books', 'books.id = transactions.book_id', 'left')
            ->where('transactions.user_id', $userId)
            ->groupStart()
                ->where('transactions.return_date', null)
                ->orWhere('transactions.return_date', '')
            ->groupEnd()
            ->orderBy('transactions.borrow_date', 'DESC')
            ->findAll();
    }

    public function markReturned(int $transactionId): bool
    {
        return $this->update($transactionId, [
            'return_date' => date('Y-m-d'),
            'status'      => 'returned',
            'fine_amount' => 0,
        ]);
    }

    public function getHistoryByUser(int $userId): array
    {
        return $this->select('
                transactions.*,
                books.title  AS book_title,
                books.author AS book_author,
                books.cover  AS book_cover
            ')
            ->join('books', 'books.id = transactions.book_id', 'left')
            ->where('transactions.user_id', $userId)
            ->orderBy('transactions.borrow_date', 'DESC')
            ->findAll();
    }

    public function createBorrow(int $userId, int $bookId, int $days = 7): int
    {
        $borrowDate = date('Y-m-d');
        $dueDate    = date('Y-m-d', strtotime("+$days days"));

        $this->insert([
            'user_id'      => $userId,
            'book_id'      => $bookId,
            'borrow_date'  => $borrowDate,
            'due_date'     => $dueDate,
            'return_date'  => null,
            'fine_amount'  => 0,
            'status'       => 'borrowed',
        ]);

        return (int) $this->getInsertID();
    }

}
