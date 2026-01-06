<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session('user');
        $role = strtolower((string) ($user['role'] ?? ''));

        $allowed = array_map(
            fn($r) => strtolower((string) $r),
            (array) ($arguments ?? [])
        );

        if (!$role || (!empty($allowed) && !in_array($role, $allowed, true))) {
            return redirect()->to('/')
                ->with('error', 'Akses ditolak (role tidak sesuai).')
                ->with('openModal', 'login');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
