<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ApiClient;

class Auth extends BaseController
{
    private ApiClient $api;

    public function __construct()
    {
        $this->api = new ApiClient();
    }

    /**
     * Fallback page (kalau kamu masih mau keep halaman login standalone).
     * Flow utama tetap splash (/).
     */
    public function login()
    {
        return view('auth/login', [
            'title'        => 'Login • Perpus Digital',
            'defaultEmail' => 'admin@library.com',
        ]);
    }

    /**
     * Fallback page (kalau masih keep register standalone).
     */
    public function register()
    {
        return view('auth/register', [
            'title' => 'Register • Perpus Digital',
        ]);
    }

    /**
     * Splash (landing) yang berisi tombol Login/Register + modal.
     * Ini yang kamu taruh di route GET '/'
     */
    public function splash()
    {
        $flashError   = session()->getFlashdata('error');
        $flashSuccess = session()->getFlashdata('success');
        $openModal    = session()->getFlashdata('openModal'); // 'login' / 'register'

        return view('auth/splash', [
            'title'        => 'BookHouse • Welcome',
            'defaultEmail' => 'admin@library.com',
            'flashError'   => $flashError,
            'flashSuccess' => $flashSuccess,
            'openModal'    => $openModal,
            'errors'       => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function doLogin()
    {
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return redirect()->to('/')
                ->with('error', 'Email dan password wajib diisi.')
                ->with('openModal', 'login');
        }

        // bersihin session lama
        session()->remove(['token', 'user']);

        // login ke backend
        $res = $this->api->post('/api/auth/login', [
            'json' => ['email' => $email, 'password' => $password],
        ]);

        $payload = $res['data'] ?? [];

        if (!($res['ok'] ?? false) || !($payload['success'] ?? false)) {
            $msg = $payload['message'] ?? ('Login gagal. HTTP ' . ($res['status'] ?? 0));
            return redirect()->to('/')
                ->with('error', $msg)
                ->with('openModal', 'login');
        }

        $token = $payload['data']['token'] ?? null;
        $user  = $payload['data']['user']  ?? null;

        if (!$token) {
            return redirect()->to('/')
                ->with('error', 'Token tidak ditemukan dari backend.')
                ->with('openModal', 'login');
        }

        session()->set('token', $token);

        // Jangan bikin login gagal hanya karena /me bermasalah
        if (!$user) {
            $me = $this->api->get('/api/auth/me');
            $mePayload = $me['data'] ?? [];

            if (($me['ok'] ?? false) && ($mePayload['success'] ?? false) && isset($mePayload['data'])) {
                $user = $mePayload['data'];
            }
        }

        if (!$user || !is_array($user)) {
            // token boleh ada, tapi kalau data user ga valid -> paksa login ulang biar aman
            session()->remove(['token', 'user']);

            return redirect()->to('/')
                ->with('error', 'Login berhasil, tapi data user gagal dibaca.')
                ->with('openModal', 'login');
        }

        session()->set('user', $user);

        $role = strtolower((string) ($user['role'] ?? ''));

        // librarian -> halaman librarian
        // member -> halaman member
        if ($role === 'librarian') {
            return redirect()->to('/librarian/dashboard')->with('success', 'Login berhasil.');
        }

        return redirect()->to('/member/dashboard')->with('success', 'Login berhasil.');
    }

    public function doRegister()
    {
        $name     = trim((string) $this->request->getPost('name'));
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $role     = trim((string) $this->request->getPost('role')) ?: 'member'; // kalau di modal ada role

        if ($name === '' || $email === '' || $password === '') {
            return redirect()->to('/')
                ->with('error', 'Nama, email, dan password wajib diisi.')
                ->with('openModal', 'register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/')
                ->with('error', 'Format email tidak valid.')
                ->with('openModal', 'register');
        }

        if (strlen($password) < 6) {
            return redirect()->to('/')
                ->with('error', 'Password minimal 6 karakter.')
                ->with('openModal', 'register');
        }

        // register ke backend
        $res = $this->api->post('/api/auth/register', [
            'json' => [
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
                // kalau backend kamu support role, ini berguna
                'role'     => $role,
            ],
        ]);

        $payload = $res['data'] ?? [];

        if (!($res['ok'] ?? false) || !($payload['success'] ?? false)) {
            $msg = $payload['message'] ?? ('Register gagal. HTTP ' . ($res['status'] ?? 0));
            return redirect()->to('/')
                ->with('error', $msg)
                ->with('openModal', 'register');
        }

        // selesai register -> balik splash, buka modal login
        return redirect()->to('/')
            ->with('success', 'Register berhasil. Silakan login.')
            ->with('openModal', 'login');
    }

    public function logout()
    {
        if (session('token')) {
            // kalau backend error, ga masalah: kita tetap clear session lokal
            $this->api->post('/api/auth/logout');
        }

        session()->destroy();

        return redirect()->to('/')
            ->with('success', 'Logout berhasil.')
            ->with('openModal', 'login');
    }
}
