<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');

        // contoh pakai: ['filter' => 'role:librarian']
        $required = $arguments[0] ?? null;

        if ($required && $role !== $required) {
            return redirect()->to('/dashboard')->with('message', 'Akses ditolak (role tidak sesuai).');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
