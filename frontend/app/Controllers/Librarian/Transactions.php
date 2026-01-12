<?php

namespace App\Controllers\Librarian;

use App\Controllers\BaseController;
use App\Models\TransactionModel;

class Transactions extends BaseController
{
    private const FINE_OVERDUE = 10000;

    private TransactionModel $tx;

    public function __construct()
    {
        $this->tx = new TransactionModel();
    }

    public function index()
    {
        $q      = trim((string) $this->request->getGet('search'));
        $status = trim((string) $this->request->getGet('status')); // borrowed|returned|overdue|'' (optional)
        $from   = trim((string) $this->request->getGet('from'));   // YYYY-MM-DD
        $to     = trim((string) $this->request->getGet('to'));     // YYYY-MM-DD

        $page = (int) ($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;

        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $db = db_connect();

        // Base query
        $builder = $db->table('transactions t')
            ->select([
                't.*',
                'u.name as member_name',
                'u.email as member_email',
                // kalau books ada:
                'b.title as book_title',
            ])
            ->join('users u', 'u.id = t.user_id', 'left');

        // join books aman: kalau tabel books tidak ada, comment 1 baris ini
        $builder->join('books b', 'b.id = t.book_id', 'left');

        // =========================
        // Filters
        // =========================
        if ($q !== '') {
            $builder->groupStart()
                ->like('u.name', $q)
                ->orLike('u.email', $q)
                ->orLike('b.title', $q)
                ->orLike('t.id', $q)
                ->groupEnd();
        }

        // date range based on borrow_date
        if ($from !== '') {
            $builder->where('t.borrow_date >=', $from . ' 00:00:00');
        }
        if ($to !== '') {
            $builder->where('t.borrow_date <=', $to . ' 23:59:59');
        }

        // status:
        // - kalau status DB kamu sudah "returned"/"borrowed", pakai langsung
        // - kalau kamu mau "overdue" dihitung otomatis, kita handle setelah fetch
        if ($status !== '' && $status !== 'overdue') {
            $builder->where('t.status', $status);
        }

        // total
        $total = (clone $builder)->countAllResults(false);

        // fetch paged
        $rows = $builder
            ->orderBy('t.borrow_date', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        // =========================
        // Normalize + auto overdue
        // =========================
        $now = time();
        $items = [];

        foreach ($rows as $r) {
            $borrowedAt = !empty($r['borrow_date']) ? strtotime($r['borrow_date']) : null;
            $dueAt      = !empty($r['due_date']) ? strtotime($r['due_date']) : null;
            $returnedAt = !empty($r['return_date']) ? strtotime($r['return_date']) : null;

            // status normalize
            $calcStatus = $r['status'] ?? '';
            if ($returnedAt) {
                $calcStatus = 'returned';
            } elseif ($dueAt && $now > $dueAt) {
                $calcStatus = 'overdue';
            } else {
                // default borrowed kalau belum return
                $calcStatus = $calcStatus !== '' ? $calcStatus : 'borrowed';
            }

            // kalau user filter overdue, buang yang bukan overdue
            if ($status === 'overdue' && $calcStatus !== 'overdue') {
                continue;
            }

            $items[] = [
                'id'          => (int) ($r['id'] ?? 0),
                'status'      => $calcStatus,
                'fine'        => ($calcStatus === 'overdue') ? self::FINE_OVERDUE : (int)($r['fine_amount'] ?? 0),

                'borrowed_at' => $r['borrow_date'] ?? null,
                'due_at'      => $r['due_date'] ?? null,
                'returned_at' => $r['return_date'] ?? null,

                'member' => [
                    'name'  => $r['member_name'] ?? '-',
                    'email' => $r['member_email'] ?? '',
                ],
                'book' => [
                    'title' => $r['book_title'] ?? '-',
                ],
            ];
        }

        $lastPage = (int) ceil(max($total, 1) / $perPage);
        if ($lastPage < 1) $lastPage = 1;

        return view('librarian/Transactions/index', [
            'items' => $items,
            'filters' => [
                'search' => $q,
                'status' => $status,
                'from'   => $from,
                'to'     => $to,
            ],
            'pager' => [
                'total'        => (int) $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => $lastPage,
                'from'         => $total ? ($offset + 1) : 0,
                'to'           => $total ? min($offset + $perPage, $total) : 0,
            ],
            'flash' => [
                'success' => session()->getFlashdata('success'),
                'error'   => session()->getFlashdata('error'),
                'info'    => session()->getFlashdata('info'),
            ],
        ]);
    }

    // ✅ POST /librarian/transactions/{id}/return
    public function markReturned(int $id)
    {
        $id = (int) $id;
        if ($id < 1) {
            return redirect()->to(site_url('librarian/transactions'))->with('error', 'ID transaksi tidak valid');
        }

        $row = $this->tx->find($id);
        if (!$row) {
            return redirect()->to(site_url('librarian/transactions'))->with('error', 'Transaksi tidak ditemukan');
        }

        $now = date('Y-m-d H:i:s');

        // hitung overdue fine (opsional)
        $dueAt = !empty($row['due_date']) ? strtotime($row['due_date']) : null;
        $fine  = 0;
        if ($dueAt && time() > $dueAt) {
            $fine = self::FINE_OVERDUE;
        }

        $ok = $this->tx->update($id, [
            'return_date' => $now,
            'status'      => 'returned',
            'fine_amount' => $fine,
        ]);

        if (!$ok) {
            return redirect()->to(site_url('librarian/transactions'))->with('error', "Gagal update transaksi #{$id}");
        }

        return redirect()->to(site_url('librarian/transactions'))->with('success', "Transaksi #{$id} berhasil ditandai Returned.");
    }
}
