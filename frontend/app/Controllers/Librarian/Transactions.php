<?php

namespace App\Controllers\Librarian;

use App\Controllers\BaseController;

class Transactions extends BaseController
{
    private const FINE_OVERDUE = 10000;

    public function index()
    {
        $q      = trim((string) $this->request->getGet('search'));
        $status = trim((string) $this->request->getGet('status')); // borrowed|returned|overdue|''
        $from   = trim((string) $this->request->getGet('from'));   // YYYY-MM-DD
        $to     = trim((string) $this->request->getGet('to'));     // YYYY-MM-DD

        // ✅ kamu maunya: dummy hanya aktif kalau ada ?dummy1
        $dummy = $this->request->getGet('dummy1') !== null ? 1 : 0;

        // =========================
        // 1) SOURCE DATA
        // =========================
        // Default HARUS kosong sampai user input data beneran
        if ($dummy === 1) {
            $rows = $this->dummyTransactions(); // baca session kalau ada
        } else {
            $rows = []; // ✅ default kosong
        }

        // =========================
        // 2) NORMALIZE + AUTO STATUS + FINE
        // =========================
        $now = time();

        $items = [];
        foreach ($rows as $row) {
            $row = $this->normalizeRow($row);

            $borrowedAt = $row['borrowed_at'] ? strtotime($row['borrowed_at']) : null;
            $dueAt      = $row['due_at'] ? strtotime($row['due_at']) : null;
            $returnedAt = $row['returned_at'] ? strtotime($row['returned_at']) : null;

            // Auto status:
            if ($returnedAt) {
                $row['status'] = 'returned';
            } elseif ($dueAt && $now > $dueAt) {
                $row['status'] = 'overdue';
            } else {
                $row['status'] = 'borrowed';
            }

            // Fine:
            $row['fine'] = ($row['status'] === 'overdue') ? self::FINE_OVERDUE : 0;

            // =========================
            // 3) FILTERS
            // =========================
            if ($q !== '') {
                $hay = strtolower(
                    ($row['id'] ?? '') . ' ' .
                        ($row['member']['name'] ?? '') . ' ' .
                        ($row['member']['email'] ?? '') . ' ' .
                        ($row['book']['title'] ?? '')
                );
                if (strpos($hay, strtolower($q)) === false) continue;
            }

            if ($status !== '' && strtolower($row['status']) !== strtolower($status)) continue;

            // date range based on borrowed_at
            if ($from !== '' && $borrowedAt) {
                $fromTs = strtotime($from . ' 00:00:00');
                if ($borrowedAt < $fromTs) continue;
            }
            if ($to !== '' && $borrowedAt) {
                $toTs = strtotime($to . ' 23:59:59');
                if ($borrowedAt > $toTs) continue;
            }

            $items[] = $row;
        }

        // sort newest first by borrowed_at
        usort($items, function ($a, $b) {
            $ta = $a['borrowed_at'] ? strtotime($a['borrowed_at']) : 0;
            $tb = $b['borrowed_at'] ? strtotime($b['borrowed_at']) : 0;
            return $tb <=> $ta;
        });

        $data = [
            'items' => $items,
            'filters' => [
                'search' => $q,
                'status' => $status,
                'from'   => $from,
                'to'     => $to,
                'dummy'  => $dummy, // ✅ supaya view bisa persist dummy1 di form
            ],
            'flash' => [
                'success' => session()->getFlashdata('success'),
                'error'   => session()->getFlashdata('error'),
                'info'    => session()->getFlashdata('info'),
            ],
        ];

        return view('librarian/Transactions/index', $data);
    }

    // ✅ ROUTE: POST /librarian/transactions/(:num)/return
    public function markReturned(int $id)
    {
        // ✅ baca dummy dari ?dummy1 (bukan ?dummy=1)
        $dummy = $this->request->getGet('dummy1') !== null ? 1 : 0;

        if ($dummy === 1) {
            $list = session()->get('tx_dummy') ?? $this->dummyTransactions();
            $now  = date('Y-m-d H:i:s');

            foreach ($list as &$row) {
                if ((int)($row['id'] ?? 0) === $id) {
                    $row['returned_at'] = $now;
                    break;
                }
            }
            unset($row);

            session()->set('tx_dummy', $list);

            session()->setFlashdata('success', "Transaction #{$id} berhasil ditandai Returned.");
            return redirect()->to(site_url('librarian/transactions?dummy1'));
        }

        // Real mode: nanti ganti dengan API update returned_at
        session()->setFlashdata('success', "Transaction #{$id} berhasil ditandai Returned.");
        return redirect()->to(site_url('librarian/transactions'));
    }

    // -------------------------
    // Helpers
    // -------------------------
    private function normalizeRow(array $row): array
    {
        $row['id'] = (int)($row['id'] ?? 0);

        $row['member'] = $row['member'] ?? [
            'name'  => $row['member_name'] ?? '-',
            'email' => $row['member_email'] ?? '',
        ];

        $row['book'] = $row['book'] ?? [
            'title' => $row['book_title'] ?? '-',
        ];

        $row['borrowed_at'] = $row['borrowed_at'] ?? $row['created_at'] ?? null;
        $row['due_at']      = $row['due_at'] ?? null;
        $row['returned_at'] = $row['returned_at'] ?? null;

        return $row;
    }

    private function dummyTransactions(): array
    {
        // ✅ session dipakai agar status terasa berubah setelah confirm
        $saved = session()->get('tx_dummy');
        if (is_array($saved) && !empty($saved)) return $saved;

        return [
            [
                'id' => 101,
                'member' => ['name' => 'Natan', 'email' => 'natan@mail.com'],
                'book' => ['title' => 'Atomic Habits'],
                'borrowed_at' => '2026-01-03 10:12:00',
                'due_at' => '2026-01-10 00:00:00',
                'returned_at' => null,
            ],
            [
                'id' => 102,
                'member' => ['name' => 'Alya', 'email' => 'alya@mail.com'],
                'book' => ['title' => "Don't Make Me Think"],
                'borrowed_at' => '2025-12-28 08:40:00',
                'due_at' => '2026-01-03 00:00:00',
                'returned_at' => '2026-01-03 15:20:00',
            ],
            [
                'id' => 103,
                'member' => ['name' => 'Raka', 'email' => 'raka@mail.com'],
                'book' => ['title' => 'The Power of Habit'],
                'borrowed_at' => '2025-12-20 09:15:00',
                'due_at' => '2025-12-27 00:00:00',
                'returned_at' => null,
            ],
        ];
    }
}
