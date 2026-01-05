<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // role disimpan saat login: session('user')['role']
        $user = session('user');
        $role = is_array($user) ? ($user['role'] ?? null) : null;

        if ($role !== 'admin') {
            return redirect()->to('/')->with('error', 'Akses ditolak. Admin/Librarian saja.');
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
