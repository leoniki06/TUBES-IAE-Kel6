<?php

namespace App\Controllers\Librarian;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Members extends BaseController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function index()
    {
        $q    = trim((string) $this->request->getGet('search'));
        $page = (int) ($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;

        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        // ✅ builder() dari model sudah otomatis FROM users
        $builder = $this->users->builder();

        // filter role
        $builder->where('role', 'member');

        // opsional: hanya yang aktif
        // $builder->where('is_active', 1);

        if ($q !== '') {
            $builder->groupStart()
                ->like('name', $q)
                ->orLike('email', $q)
                ->orLike('phone', $q)
            ->groupEnd();
        }

        $builder->orderBy('id', 'DESC');

        // ✅ total (clone biar query limit ga ikut)
        $total = (clone $builder)->countAllResults(false);

        // paging
        $rows = $builder->limit($perPage, $offset)->get()->getResultArray();

        $lastPage = (int) ceil(max($total, 1) / $perPage);
        if ($lastPage < 1) $lastPage = 1;

        $normalized = [
            'items'        => $rows,
            'total'        => (int) $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => $lastPage,
            'from'         => $total ? ($offset + 1) : 0,
            'to'           => $total ? min($offset + $perPage, $total) : 0,
        ];

        return view('librarian/members/index', [
            'q'       => $q,
            'members' => $normalized,
            'flash'   => [
                'success' => session()->getFlashdata('success'),
                'error'   => session()->getFlashdata('error'),
                'info'    => session()->getFlashdata('info'),
            ],
        ]);
    }

    // ✅ nonaktifkan member (bukan hapus row)
    public function delete($id)
    {
        $id = (int) $id;
        if ($id < 1) {
            return redirect()->to('/librarian/members')->with('error', 'ID tidak valid');
        }

        $user = $this->users->find($id);
        if (!$user || ($user['role'] ?? '') !== 'member') {
            return redirect()->to('/librarian/members')->with('error', 'Member tidak ditemukan');
        }

        $ok = $this->users->update($id, ['is_active' => 0]);

        if (!$ok) {
            return redirect()->to('/librarian/members')->with('error', 'Gagal menonaktifkan member');
        }

        return redirect()->to('/librarian/members')->with('success', 'Member dinonaktifkan');
    }
}
