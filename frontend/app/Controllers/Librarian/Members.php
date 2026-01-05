<?php

namespace App\Controllers\Librarian;

use App\Controllers\BaseController;
use App\Libraries\ApiClient;

class Members extends BaseController
{
    private ApiClient $api;

    public function __construct()
    {
        $this->api = new ApiClient();
    }

    private function unwrap(array $res): array
    {
        $payload = $res['data'] ?? [];

        return [
            'ok'      => (bool) ($res['ok'] ?? false),
            'status'  => (int) ($res['status'] ?? 0),
            'success' => (bool) ($payload['success'] ?? false),
            'message' => (string) ($payload['message'] ?? ''),
            'data'    => $payload['data'] ?? null,
            'errors'  => $payload['errors'] ?? [],
        ];
    }

    public function index()
    {
        $q    = trim((string) $this->request->getGet('search'));
        $page = (int) ($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;

        $res = $this->api->get('/api/members', [
            'query' => array_filter([
                'search' => $q !== '' ? $q : null,
                'page'   => $page,
            ])
        ]);

        // ✅ KUNCI: 401 = token kosong/invalid/expired
        if (($res['status'] ?? 0) === 401) {
            session()->remove('token');

            // kalau kamu punya halaman login CI4:
            // return redirect()->to('/auth/login')->with('error', 'Sesi habis. Login ulang.');

            return view('librarian/members/index', [
                'q'       => $q,
                'members' => $this->emptyPager(),
                'flash'   => ['type' => 'error', 'msg' => 'HTTP 401 (Unauthorized). Token tidak ada / tidak valid. Silakan login ulang.'],
            ]);
        }

        // kalau API gagal (HTTP 0/500/dll)
        if (!($res['ok'] ?? false)) {
            $msg = 'Gagal akses API members. HTTP ' . ($res['status'] ?? 0);
            if (!empty($res['error'])) $msg .= ' — ' . $res['error'];

            return view('librarian/members/index', [
                'q'       => $q,
                'members' => $this->emptyPager(),
                'flash'   => ['type' => 'error', 'msg' => $msg],
            ]);
        }

        $payload = $res['data'] ?? [];
        if (!($payload['success'] ?? false)) {
            return view('librarian/members/index', [
                'q'       => $q,
                'members' => $this->emptyPager(),
                'flash'   => ['type' => 'error', 'msg' => $payload['message'] ?? 'API error'],
            ]);
        }

        $p = $payload['data'] ?? [];

        $normalized = [
            'items'        => $p['data'] ?? [],
            'total'        => (int)($p['total'] ?? 0),
            'per_page'     => (int)($p['per_page'] ?? 10),
            'current_page' => (int)($p['current_page'] ?? 1),
            'last_page'    => (int)($p['last_page'] ?? 1),
            'from'         => (int)($p['from'] ?? 0),
            'to'           => (int)($p['to'] ?? 0),
        ];

        return view('librarian/members/index', [
            'q'       => $q,
            'members' => $normalized,
        ]);
    }

    private function emptyPager(): array
    {
        return [
            'items'        => [],
            'total'        => 0,
            'per_page'     => 10,
            'current_page' => 1,
            'last_page'    => 1,
            'from'         => 0,
            'to'           => 0,
        ];
    }

    // ✅ satu-satunya aksi: nonaktifkan
    public function delete($id)
    {
        $res = $this->api->delete("/api/members/{$id}");
        $r = $this->unwrap($res);

        if (($r['status'] ?? 0) === 401) {
            session()->remove('token');
            return redirect()->to('/auth/login')->with('error', 'Sesi habis. Login ulang.');
        }

        if (!$r['ok'] || !$r['success']) {
            return redirect()->to('/librarian/members')
                ->with('error', $r['message'] ?: 'Gagal menonaktifkan member');
        }

        return redirect()->to('/librarian/members')->with('success', 'Member dinonaktifkan');
    }
}
