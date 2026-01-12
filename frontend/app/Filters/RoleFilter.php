<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session('user');

        // Kalau session user tidak valid, arahin ke splash + modal login
        if (!is_array($user) || empty($user['id'])) {
            session()->remove(['token', 'user', 'user_id', 'role', 'isLoggedIn']);

            return redirect()->to(base_url('/'))
                ->with('error', 'Silakan login terlebih dahulu.')
                ->with('openModal', 'login');
        }

        // Ambil role dari beberapa kemungkinan bentuk payload
        $role = $user['role'] ?? session('role') ?? '';

        // role bisa berupa array dari API tertentu
        if (is_array($role)) {
            $role = $role['name'] ?? '';
        }

        $role = strtolower(trim((string) $role));

        // Allowed roles dari arguments filter route: role:librarian / role:member
        $allowed = array_map(
            static fn($r) => strtolower(trim((string) $r)),
            (array) $arguments
        );

        // Kalau route tidak ngasih allowed role, anggap lolos
        if (empty($allowed)) {
            return null;
        }

        // Role kosong atau tidak termasuk allowed → akses ditolak
        if ($role === '' || !in_array($role, $allowed, true)) {
            // jangan destroy session kalau user valid tapi role tidak cocok
            return redirect()->to(base_url('/'))
                ->with('error', 'Akses ditolak.')
                ->with('openModal', 'login');
        }

        // Sinkronkan session role untuk konsistensi
        if (!session('role')) {
            session()->set('role', $role);
        }
        if (!session('user_id')) {
            session()->set('user_id', $user['id']);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
