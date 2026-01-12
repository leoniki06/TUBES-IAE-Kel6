<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\BookModel;

class BorrowController extends BaseController
{
    protected TransactionModel $trxModel;
    protected BookModel $bookModel;

    public function __construct()
    {
        $this->trxModel  = new TransactionModel();
        $this->bookModel = new BookModel();
    }

    // helper ambil user_id dari session (format session kamu kadang beda)
    private function getUserId(): int
    {
        $u = session('user');

        if (is_array($u) && isset($u['id'])) return (int)$u['id'];

        $id = (int)(session('user.id') ?? 0);
        return $id;
    }

    // =========================
    // HALAMAN BUKU DIPINJAM (ACTIVE)
    // =========================
    public function borrowed()
    {
        $userId = $this->getUserId();
        if ($userId <= 0) return redirect()->to('/');

        $rows = $this->trxModel->getActiveBorrowed($userId);

        return view('member/borrowed', [
            'title' => 'Buku Dipinjam',
            'rows'  => $rows,
        ]);
    }

    // =========================
    // HALAMAN HISTORY / RIWAYAT TRANSAKSI
    // =========================
    public function history()
    {
        $userId = $this->getUserId();
        if ($userId <= 0) return redirect()->to('/');

        // pastikan kamu sudah tambahkan function ini di TransactionModel:
        // getHistoryByUser($userId)
        $rows = $this->trxModel->getHistoryByUser($userId);

        return view('member/history', [
            'title' => 'Riwayat Transaksi',
            'rows'  => $rows,
        ]);
    }

    // =========================
    // PROSES PINJAM (SAVE)
    // POST /member/borrow/save
    // =========================
    public function save()
    {
        $userId = $this->getUserId();
        if ($userId <= 0) return redirect()->to('/');

        $bookId = (int)($this->request->getPost('book_id') ?? 0);
        if ($bookId <= 0) {
            return redirect()->back()->with('error', 'Book ID tidak valid.');
        }

        // cek stok tersedia
        $book = $this->bookModel->find($bookId);
        if (!$book) return redirect()->back()->with('error', 'Buku tidak ditemukan.');

        $stockAvail = (int)($book['stock_available'] ?? 0);
        if ($stockAvail <= 0) return redirect()->back()->with('error', 'Stok buku habis.');

        // cek limit pinjam aktif (misal max 3)
        $active = $this->trxModel->getActiveBorrowed($userId);
        if (count($active) >= 3) {
            return redirect()->back()->with('error', 'Limit pinjam 3 buku sudah tercapai.');
        }

        $borrowDate = date('Y-m-d');
        $dueDate    = date('Y-m-d', strtotime('+7 days'));

        // simpan transaksi
        $ok = $this->trxModel->insert([
            'user_id'     => $userId,
            'book_id'     => $bookId,
            'borrow_date' => $borrowDate,
            'due_date'    => $dueDate,
            'return_date' => null,
            'fine_amount' => 0,
            'status'      => 'borrowed',
        ]);

        if (!$ok) return redirect()->back()->with('error', 'Gagal menyimpan transaksi.');

        // kurangi stok
        $this->bookModel->update($bookId, [
            'stock_available' => $stockAvail - 1
        ]);

        return redirect()->to('/member/borrowed')->with('success', 'Berhasil meminjam buku!');
    }

    // =========================
    // PROSES KEMBALIKAN
    // POST /member/return-process/{id}
    // =========================
    public function processReturn($transactionId)
    {
        $userId = $this->getUserId();
        if ($userId <= 0) return redirect()->to('/');

        $transactionId = (int)$transactionId;
        if ($transactionId <= 0) return redirect()->back()->with('error', 'ID transaksi tidak valid.');

        // ambil transaksi
        $trx = $this->trxModel->find($transactionId);
        if (!$trx) return redirect()->back()->with('error', 'Transaksi tidak ditemukan.');

        // pengaman: pastikan transaksi milik user ini
        if ((int)$trx['user_id'] !== $userId) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // mark returned
        $this->trxModel->markReturned($transactionId);

        // tambah stok buku
        $bookId = (int)($trx['book_id'] ?? 0);
        if ($bookId > 0) {
            $book = $this->bookModel->find($bookId);
            if ($book) {
                $stockAvail = (int)($book['stock_available'] ?? 0);
                $this->bookModel->update($bookId, [
                    'stock_available' => $stockAvail + 1
                ]);
            }
        }

        return redirect()->to('/member/return')->with('success', 'Buku berhasil dikembalikan.');
    }
}
