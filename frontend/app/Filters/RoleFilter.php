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

        if (!is_array($user)) {
            return redirect()->to('/')->with('openModal', 'login');
        }

        $role = strtolower((string) ($user['role'] ?? ''));

        $allowed = array_map('strtolower', (array) $arguments);

        if (!$role || !in_array($role, $allowed, true)) {
            return redirect()->to('/')
                ->with('error', 'Akses ditolak.')
                ->with('openModal', 'login');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
