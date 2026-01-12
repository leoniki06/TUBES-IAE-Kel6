<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Ambil session penting
        $isLoggedIn = (bool) session('isLoggedIn');
        $user       = session('user');
        $token      = (string) session('token');

        // Validasi bentuk user
        $userId = null;
        if (is_array($user)) {
            $userId = $user['id'] ?? null;
        }

        // ✅ kondisi dianggap login valid
        $valid = $isLoggedIn && is_array($user) && !empty($userId);

        // Optional (recommended): pastikan token juga ada
        // Kalau kamu memang ada halaman yang tidak butuh token, kamu bisa matikan cek token ini
        if ($valid && $token === '') {
            // token kosong = sesi palsu / belum lengkap
            $valid = false;
        }

        if (!$valid) {
            // bersihin sesi supaya gak ada "fake login"
            session()->remove(['token', 'user', 'user_id', 'role', 'isLoggedIn']);

            // Redirect ke splash + buka modal login
            return redirect()->to(base_url('/'))
                ->with('error', 'Silakan login terlebih dahulu.')
                ->with('openModal', 'login');
        }

        // sinkronkan helper session biar controller transaksi gampang
        // (tidak overwrite kalau sudah ada)
        if (!session('user_id')) {
            session()->set('user_id', $userId);
        }
        if (!session('role')) {
            $role = $user['role'] ?? '';
            if (is_array($role)) {
                $role = $role['name'] ?? '';
            }
            session()->set('role', strtolower((string) $role));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
