<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\BookModel;

class ReturnController extends BaseController
{
    protected TransactionModel $tm;
    protected BookModel $bm;

    public function __construct()
    {
        $this->tm = new TransactionModel();
        $this->bm = new BookModel();
    }

    // =========================
    // HALAMAN RETURN (LIST YANG BISA DIKEMBALIKAN)
    // =========================
    public function index()
    {
        $user = session('user');
        if (!$user || !isset($user['id'])) {
            return redirect()->to('/');
        }

        $userId = (int) $user['id'];

        // transaksi aktif user (belum return)
        $active = $this->tm->getActiveBorrowed($userId);

        // summary
        $count = count($active);

        $nearestDue = null;
        foreach ($active as $t) {
            if (!empty($t['due_date'])) {
                $d = strtotime($t['due_date']);
                if ($nearestDue === null || $d < $nearestDue) $nearestDue = $d;
            }
        }

        return view('member/return', [
            'title'      => 'Pengembalian Buku',
            'user'       => $user,
            'active'     => $active,
            'count'      => $count,
            'nearestDue' => $nearestDue ? date('d M', $nearestDue) : '-',
        ]);
    }

    // =========================
    // PROSES RETURN
    // =========================
    public function process($transactionId)
    {
        $user = session('user');
        if (!$user || !isset($user['id'])) {
            return redirect()->to('/');
        }

        $userId = (int) $user['id'];
        $transactionId = (int) $transactionId;

        // ambil transaksi by id
        $trx = $this->tm->find($transactionId);
        if (!$trx) {
            return redirect()->to('member/return')->with('error', 'Transaksi tidak ditemukan.');
        }

        // pastikan transaksi punya user yang sama
        if ((int)$trx['user_id'] !== $userId) {
            return redirect()->to('member/return')->with('error', 'Akses ditolak.');
        }

        // kalau sudah returned, jangan diproses lagi
        if (!empty($trx['return_date']) || ($trx['status'] ?? '') === 'returned') {
            return redirect()->to('member/return')->with('info', 'Buku ini sudah dikembalikan.');
        }

        // update transaksi: returned
        $ok = $this->tm->markReturned($transactionId);

        if (!$ok) {
            return redirect()->to('member/return')->with('error', 'Gagal memproses pengembalian.');
        }

        // stok_available buku +1
        $bookId = (int) ($trx['book_id'] ?? 0);
        if ($bookId > 0) {
            $this->bm->where('id', $bookId)->increment('stock_available', 1);
        }

        return redirect()->to('member/return')->with('success', 'Buku berhasil dikembalikan ✅');
    }
}
