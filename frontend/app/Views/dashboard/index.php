<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $u = session('user') ?? [];
        return view('dashboard/index', [
            'title' => 'Dashboard • Library',
            'pageTitle' => 'Dashboard',
            'pageSub' => 'Ringkasan portal perpustakaan digital',
            'user' => $u,
        ]);
    }
}
