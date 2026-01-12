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
     * Halaman splash (landing)
     * Menampilkan modal login/register
     */
    public function splash()
    {
        return view('auth/splash', [
            'title'        => 'BookHouse • Welcome',
            'flashError'   => session()->getFlashdata('error'),
            'flashSuccess' => session()->getFlashdata('success'),
            'openModal'    => session()->getFlashdata('openModal'),
        ]);
    }

    /**
     * =========================
     * ✅ GET LOGIN PAGE
     * Route: GET auth/login
     * =========================
     * Karena sistem kamu sebenarnya login lewat modal di splash,
     * route ini kita arahkan ke splash dan buka modal login.
     */
    public function login()
    {
        // kalau sudah login, arahkan sesuai role
        $user = session('user');
        if (is_array($user) && !empty($user['role'])) {
            $role = strtolower((string) $user['role']);
            return $role === 'librarian'
                ? redirect()->to('/librarian/dashboard')
                : redirect()->to('/member/dashboard');
        }

        return redirect()->to('/')->with('openModal', 'login');
    }

    /**
     * =========================
     * ✅ GET REGISTER PAGE
     * Route: GET auth/register
     * =========================
     */
    public function register()
    {
        // kalau sudah login, arahkan sesuai role
        $user = session('user');
        if (is_array($user) && !empty($user['role'])) {
            $role = strtolower((string) $user['role']);
            return $role === 'librarian'
                ? redirect()->to('/librarian/dashboard')
                : redirect()->to('/member/dashboard');
        }

        return redirect()->to('/')->with('openModal', 'register');
    }

    /**
     * =========================
     * 🔐 LOGIN (POST)
     * Route: POST auth/login
     * =========================
     */
    public function doLogin()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return redirect()->to('/')
                ->with('error', 'Email dan password wajib diisi.')
                ->with('openModal', 'login');
        }

        $res = $this->api->post('/api/auth/login', [
            'json' => compact('email', 'password'),
        ]);

        $payload = $res['data'] ?? [];

        if (!($res['ok'] ?? false) || !($payload['success'] ?? false)) {
            return redirect()->to('/')
                ->with('error', $payload['message'] ?? 'Login Gagal')
                ->with('openModal', 'login');
        }

        $user = $payload['data']['user'] ?? [];

        // role dari API bisa bentuk:
        // - $user['role']['name']
        // - atau $user['role'] string
        $role = $user['role'] ?? 'member';
        if (is_array($role)) {
            $role = $role['name'] ?? 'member';
        }
        $user['role'] = strtolower((string) $role);

        $token = (string) ($payload['data']['token'] ?? '');

        // Simpan data login
        session()->set([
            'token'      => $token,
            'user'       => $user,
            'isLoggedIn' => true
        ]);

        // Optional: supaya layout bisa sync token ke localStorage
        if ($token !== '') {
            session()->setFlashdata('token_baru', $token);
        }

        // Redirect sesuai role
        if ($user['role'] === 'librarian') {
            return redirect()->to('/librarian/dashboard')
                ->with('success', 'Login berhasil.');
        }

        return redirect()->to('/member/dashboard')
            ->with('success', 'Login berhasil.');
    }

    /**
     * =========================
     * 🧾 REGISTER (POST)
     * Route: POST auth/register
     * =========================
     */
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

        // CALL API REGISTER (LARAVEL)
        $res = $this->api->post('/api/auth/register', [
            'json' => [
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
            ],
        ]);

        $payload = $res['data'] ?? [];

        if (!($res['ok'] ?? false) || !($payload['success'] ?? false)) {
            return redirect()->to('/')
                ->with('error', $payload['message'] ?? 'Registrasi gagal.')
                ->with('openModal', 'register');
        }

        $user = $payload['data']['user'] ?? [];

        $role = $user['role'] ?? 'member';
        if (is_array($role)) {
            $role = $role['name'] ?? 'member';
        }
        $user['role'] = strtolower((string) $role);

        $token = (string) ($payload['data']['token'] ?? '');

        session()->set([
            'token'      => $token,
            'user'       => $user,
            'isLoggedIn' => true,
        ]);

        session()->regenerate();

        if ($token !== '') {
            session()->setFlashdata('token_baru', $token);
        }

        if ($user['role'] === 'librarian') {
            return redirect()->to('/librarian/dashboard')
                ->with('success', 'Registrasi berhasil. Selamat datang!');
        }

        return redirect()->to('/member/dashboard')
            ->with('success', 'Registrasi berhasil. Selamat datang!');
    }

    /**
     * =========================
     * 🚪 LOGOUT
     * Route: GET logout / GET auth/logout
     * =========================
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/')
            ->with('success', 'Logout berhasil.')
            ->with('openModal', 'login');
    }
}
