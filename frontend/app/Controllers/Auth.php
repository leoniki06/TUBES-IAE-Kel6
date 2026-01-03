<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login', ['title' => 'Login • Perpus Digital']);
    }

    public function register()
    {
        return view('auth/register', ['title' => 'Register • Perpus Digital']);
    }

    public function doLogin()
    {
        return redirect()->to(base_url('librarian/dashboard'));
    }

    public function doRegister()
    {
        return redirect()->to(base_url('auth/login'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'));
    }
}
