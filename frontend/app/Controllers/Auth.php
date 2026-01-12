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
     * Helper: cek sesi login valid
     */
    private function isSessionValid(): bool
    {
        $isLoggedIn = (bool) session('isLoggedIn');
        $user       = session('user');
        $token      = (string) session('token');

        if (!$isLoggedIn) return false;
        if (!is_array($user) || empty($user['id'])) return false;

        // recommended: kalau app kamu bergantung ke JWT, token wajib ada
        if ($token === '') return false;

        return true;
    }

    /**
     * Helper: normalize role dari API (string/array)
     */
    private function normalizeRole($role): string
    {
        if (is_array($role)) {
            $role = $role['name'] ?? '';
        }
        $role = strtolower(trim((string) $role));
        return $role !== '' ? $role : 'member';
    }

    /**
     * Helper: simpan sesi login konsisten
     */
    private function setLoginSession(array $user, string $token): void
    {
        $role = $this->normalizeRole($user['role'] ?? 'member');
        $user['role'] = $role;

        $userId = $user['id'] ?? null;

        session()->set([
            'token'      => $token,
            'user'       => $user,
            'user_id'    => $userId,   // ✅ penting untuk transaksi
            'role'       => $role,     // ✅ memudahkan RoleFilter
            'isLoggedIn' => true,
        ]);

        // ✅ cegah session fixation & state nempel pas testing
        session()->regenerate();

        // Optional: supaya layout bisa sync token ke localStorage
        if ($token !== '') {
            session()->setFlashdata('token_baru', $token);
        }
    }

    /**
     * =========================
     * ✅ GET LOGIN PAGE
     * Route: GET auth/login
     * =========================
     */
    public function login()
    {
        if ($this->isSessionValid()) {
            $role = strtolower((string) session('role'));
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
        if ($this->isSessionValid()) {
            $role = strtolower((string) session('role'));
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
        $email    = trim((string) $this->request->getPost('email'));
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

        // ✅ jika request gagal / response bukan json, tampilkan raw biar gampang debug
        if (!($res['ok'] ?? false) || !is_array($payload) || !($payload['success'] ?? false)) {
            $msg = $payload['message'] ?? 'Login gagal.';
            if (!empty($res['error'])) $msg .= ' ' . $res['error'];
            if (($res['status'] ?? 0) === 0 && !empty($res['raw'])) $msg .= ' (Raw: ' . substr($res['raw'], 0, 120) . '...)';

            // bersihin sesi biar gak ada login palsu
            session()->remove(['token', 'user', 'user_id', 'role', 'isLoggedIn']);

            return redirect()->to('/')
                ->with('error', $msg)
                ->with('openModal', 'login');
        }

        $user  = $payload['data']['user'] ?? [];
        $token = (string) ($payload['data']['token'] ?? '');

        // ✅ ini wajib: kalau token kosong, jangan anggap login sukses
        if (!is_array($user) || empty($user['id']) || $token === '') {
            session()->remove(['token', 'user', 'user_id', 'role', 'isLoggedIn']);

            return redirect()->to('/')
                ->with('error', 'Login gagal: data user/token tidak valid dari API.')
                ->with('openModal', 'login');
        }

        $this->setLoginSession($user, $token);

        $role = (string) session('role');

        if ($role === 'librarian') {
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

        $res = $this->api->post('/api/auth/register', [
            'json' => [
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
            ],
        ]);

        $payload = $res['data'] ?? [];

        if (!($res['ok'] ?? false) || !is_array($payload) || !($payload['success'] ?? false)) {
            $msg = $payload['message'] ?? 'Registrasi gagal.';
            if (!empty($res['error'])) $msg .= ' ' . $res['error'];

            // bersihin sesi
            session()->remove(['token', 'user', 'user_id', 'role', 'isLoggedIn']);

            return redirect()->to('/')
                ->with('error', $msg)
                ->with('openModal', 'register');
        }

        $user  = $payload['data']['user'] ?? [];
        $token = (string) ($payload['data']['token'] ?? '');

        if (!is_array($user) || empty($user['id']) || $token === '') {
            session()->remove(['token', 'user', 'user_id', 'role', 'isLoggedIn']);

            return redirect()->to('/')
                ->with('error', 'Registrasi gagal: data user/token tidak valid dari API.')
                ->with('openModal', 'register');
        }

        $this->setLoginSession($user, $token);

        $role = (string) session('role');

        if ($role === 'librarian') {
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
