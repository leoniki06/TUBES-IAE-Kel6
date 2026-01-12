<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AuthTest extends BaseController
{
    /**
     * AUTO LOGIN DUMMY
     * Buka: http://localhost:8081/test-login
     */
    public function loginDummy()
    {
        // GANTI ID INI sesuai user member yang ada di database kamu
        // Contoh: kalau di tabel users ada id=2 untuk member, ubah jadi 2
        $dummyUser = [
            'id'   => 1,
            'name' => 'Member Dummy',
            'role' => 'member',
        ];

        // bikin session seolah-olah sudah login
        session()->set([
            'user' => $dummyUser,
            'logged_in' => true,
        ]);

        return redirect()->to('/member/dashboard');
    }

    /**
     * AUTO LOGOUT
     * Buka: http://localhost:8081/test-logout
     */
    public function logoutDummy()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
