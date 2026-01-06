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

        // reset session lama
        session()->remove(['token', 'user']);

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
        $user  = $payload['data']['user'] ?? null;

        if (!$token || !is_array($user)) {
            return redirect()->to('/')
                ->with('error', 'Login gagal: token/user tidak valid dari backend.')
                ->with('openModal', 'login');
        }

        session()->set('token', $token);
        session()->set('user', $user);

        // penting: bikin session stabil setelah login
        session()->regenerate();

        $role = strtolower((string) ($user['role'] ?? ''));

        // kalau sebelumnya user sempat mau akses halaman tertentu
        $redirectTo = session()->getFlashdata('redirectTo');
        if ($redirectTo) {
            return redirect()->to('/' . ltrim($redirectTo, '/'))
                ->with('success', 'Login berhasil.');
        }

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

        $res = $this->api->post('/api/auth/register', [
            'json' => [
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
            ],
        ]);

        $payload = $res['data'] ?? [];

        if (!($res['ok'] ?? false) || !($payload['success'] ?? false)) {
            $msg = $payload['message'] ?? ('Register gagal. HTTP ' . ($res['status'] ?? 0));
            return redirect()->to('/')
                ->with('error', $msg)
                ->with('openModal', 'register');
        }

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
