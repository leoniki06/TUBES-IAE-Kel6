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
     * 🔐 LOGIN
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

        session()->remove(['token', 'user']);

        $res = $this->api->post('/api/auth/login', [
            'json' => compact('email', 'password'),
        ]);

        $payload = $res['data'] ?? [];

        if (!($res['ok'] ?? false) || !($payload['success'] ?? false)) {
            return redirect()->to('/')
                ->with('error', $payload['message'] ?? 'Login gagal.')
                ->with('openModal', 'login');
        }

        $user = $payload['data']['user'] ?? [];

        /**
         * 🔥 NORMALISASI ROLE
         */
        $role = $user['role'] ?? null;

        if (is_array($role)) {
            $role = $role['name'] ?? '';
        }

        $user['role'] = strtolower((string) $role);

        session()->set('token', $payload['data']['token']);
        session()->set('user', $user);
        session()->regenerate();

        return $user['role'] === 'librarian'
            ? redirect()->to('/librarian/dashboard')->with('success', 'Login berhasil.')
            : redirect()->to('/member/dashboard')->with('success', 'Login berhasil.');
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

        /**
         * 🔹 CALL API REGISTER (LARAVEL)
         */
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

        /**

         */
        $user = $payload['data']['user'] ?? [];
        $role = $user['role'] ?? 'member';

        if (is_array($role)) {
            $role = $role['name'] ?? 'member';
        }

        $user['role'] = strtolower((string) $role);

        session()->set('token', $payload['data']['token']);
        session()->set('user', $user);
        session()->regenerate();

        return redirect()->to('/member/dashboard')
            ->with('success', 'Registrasi berhasil. Selamat datang!');
    }

    /**
     * =========================
     * 🚪 LOGOUT
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
