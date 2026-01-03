<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // kalau sudah login → dashboard
        if (session()->get('access_token')) {
            return redirect()->to('/dashboard');
        }

        // kalau belum login → halaman login
        return redirect()->to('/auth/login');
    }
}
