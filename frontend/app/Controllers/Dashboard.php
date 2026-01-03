<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        /**
         * Dashboard GLOBAL
         * ----------------
         * Route: /dashboard
         * Fungsi:
         * - Sebagai landing page setelah login
         * - Redirect ke dashboard sesuai role
         */

        // sementara arahkan ke librarian dashboard
        // (nanti bisa pakai role dari session)
        return redirect()->to(base_url('librarian/dashboard'));
    }
}
