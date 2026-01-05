<?php

namespace App\Controllers\Librarian;

use App\Controllers\BaseController;
use App\Libraries\ApiClient;

class Dashboard extends BaseController
{
    private ApiClient $api;

    public function __construct()
    {
        $this->api = new ApiClient();
    }

    public function index()
    {
        $token = (string) (session('token') ?? '');
        if ($token === '') {
            return redirect()->to(site_url('auth/login'))->with('error', 'Silakan login dulu.');
        }

        $is401 = static function (array $res): bool {
            return !($res['ok'] ?? false) && (int)($res['status'] ?? 0) === 401;
        };

        $get = function (string $path, array $query = []) {
            return $this->api->get($path, [
                'query' => array_filter($query, static fn($v) => $v !== null && $v !== ''),
            ]);
        };

        $getTotal = static function (array $res): int {
            $payload = $res['data'] ?? [];
            $p = $payload['data'] ?? null; // laravel paginator biasanya di data

            if (is_array($p) && isset($p['total'])) return (int)$p['total'];
            if (isset($payload['total'])) return (int)$payload['total'];
            return 0;
        };

        $getItems = static function (array $res): array {
            $payload = $res['data'] ?? [];
            $p = $payload['data'] ?? null;

            if (is_array($p) && isset($p['data']) && is_array($p['data'])) return $p['data'];
            if (isset($payload['data']) && is_array($payload['data'])) return $payload['data'];
            return [];
        };

        $today = date('Y-m-d');

        // ===== fetch core
        $resBooks   = $get('/api/books', ['page' => 1]);
        $resMembers = $get('/api/members', ['page' => 1]); // ini yang harus jadi sumber dashboard members
        $resOverdue = $get('/api/loans', ['page' => 1, 'status' => 'overdue']);

        if ($is401($resBooks) || $is401($resMembers) || $is401($resOverdue)) {
            session()->remove(['token', 'user']);
            return redirect()->to(site_url('auth/login'))->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $totalBooks   = $getTotal($resBooks);
        $totalMembers = $getTotal($resMembers);
        $totalOverdue = $getTotal($resOverdue);

        // ===== transactions today
        $resTxTodayAll = $get('/api/loans', ['page' => 1, 'from' => $today, 'to' => $today]);
        if ($is401($resTxTodayAll)) {
            session()->remove(['token', 'user']);
            return redirect()->to(site_url('auth/login'))->with('error', 'Sesi habis. Silakan login lagi.');
        }
        $txTodayTotal = $getTotal($resTxTodayAll);

        $resBorrowToday  = $get('/api/loans', ['page' => 1, 'status' => 'borrowed',  'from' => $today, 'to' => $today]);
        $resReturnToday  = $get('/api/loans', ['page' => 1, 'status' => 'returned',  'from' => $today, 'to' => $today]);
        $resOverdueToday = $get('/api/loans', ['page' => 1, 'status' => 'overdue',   'from' => $today, 'to' => $today]);

        $borrowToday  = $is401($resBorrowToday)  ? 0 : $getTotal($resBorrowToday);
        $returnToday  = $is401($resReturnToday)  ? 0 : $getTotal($resReturnToday);
        $overdueToday = $is401($resOverdueToday) ? 0 : $getTotal($resOverdueToday);

        // ===== recent transactions
        $resTxRecent = $get('/api/loans', ['page' => 1]);
        if ($is401($resTxRecent)) {
            session()->remove(['token', 'user']);
            return redirect()->to(site_url('auth/login'))->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $recentItems = $getItems($resTxRecent);
        $txRecent = [];

        foreach (array_slice($recentItems, 0, 3) as $row) {
            $id = (int)($row['id'] ?? 0);
            $member = $row['member']['name'] ?? ($row['user']['name'] ?? '—');
            $book   = $row['book']['title'] ?? '—';

            $st = strtolower((string)($row['status'] ?? 'borrowed'));
            $type = ($st === 'returned') ? 'Return' : 'Borrow';

            $tRaw = $row['borrowed_at'] ?? ($row['created_at'] ?? null);
            $time = '—';
            if ($tRaw) {
                $ts = strtotime((string)$tRaw);
                if ($ts !== false) $time = date('H:i', $ts);
            }

            $txRecent[] = [
                'id'     => $id,
                'member' => (string)$member,
                'book'   => (string)$book,
                'type'   => $type,
                'status' => ucfirst($st),
                'time'   => $time,
            ];
        }

        // ===== books snapshot (page 1)
        $booksSample = $getItems($resBooks);

        $stockAvail = 0;
        $stockBorrowed = 0;

        foreach ($booksSample as $b) {
            $avail = (int)($b['stock_available'] ?? 0);
            $total = (int)($b['stock_total'] ?? 0);
            $stockAvail += $avail;
            $stockBorrowed += max(0, $total - $avail);
        }

        // ===== members snapshot HARUS dari /api/members page 1
        $membersSample = $getItems($resMembers);
        $activeSnap = 0;
        $inactiveSnap = 0;

        foreach ($membersSample as $m) {
            $isActive = (bool)($m['is_active'] ?? false);
            $isActive ? $activeSnap++ : $inactiveSnap++;
        }

        // ===== kirim ke view (BEDAKAN NAMA)
        $kpi = [
            'books_total'     => $totalBooks,
            'members_total'   => $totalMembers,
            'tx_today'        => $txTodayTotal,
            'overdue_total'   => $totalOverdue,
            'stock_avail'     => $stockAvail,
            'stock_borrowed'  => $stockBorrowed,
            'active_snap'     => $activeSnap,
            'inactive_snap'   => $inactiveSnap,
        ];

        $tx = [
            'borrow_today'   => $borrowToday,
            'return_today'   => $returnToday,
            'overdue_today'  => $overdueToday,
            'overdue_total'  => $totalOverdue,
        ];

        // Ini pager khusus (kalau view butuh items)
        $membersPager = [
            'items'        => $membersSample,
            'total'        => $totalMembers,
            'per_page'     => (int)($resMembers['data']['data']['per_page'] ?? 10),
            'current_page' => (int)($resMembers['data']['data']['current_page'] ?? 1),
            'last_page'    => (int)($resMembers['data']['data']['last_page'] ?? 1),
        ];

        // optional, kalau kamu pakai "booksMeta" di view lama
        $booksMeta = [
            'total' => $totalBooks,
        ];

        // optional, kalau view lama expect $booksList
        $booksList = $booksSample;

        return view('librarian/dashboard', [
            'kpi'         => $kpi,
            'tx'          => $tx,
            'txRecent'    => $txRecent,
            'membersPager' => $membersPager,
            'booksList'   => $booksList,
            'booksMeta'   => $booksMeta,
        ]);
    }
}
