<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $user = session('user');

        return view('member/dashboard', [
            'title' => 'Member Dashboard',
            'user'  => $user,
        ]);
    }
}

