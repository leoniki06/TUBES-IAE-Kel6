<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');
        // Ambil role yang diminta dari Routes (misal: 'member')
        $roleNeeded = $arguments[0] ?? '';

        // Jika user belum login atau role tidak sesuai, kembali ke splash
        if (!$user || strtolower($user['role']) !== strtolower($roleNeeded)) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}