<?php

namespace App\Controllers\Librarian;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        /**
         * Dashboard LIBRARIAN
         * -------------------
         * Route: /librarian/dashboard
         * Fungsi:
         * - Menampilkan dashboard operasional librarian
         * - UI sudah kamu buat di Views/librarian/dashboard.php
         */

        return view('librarian/dashboard', [
            'title' => 'Librarian Dashboard'
        ]);
    }
}
