<?php

namespace App\Controllers;

use App\Models\BookModel; // Pastikan baris ini ada!

class Member extends BaseController
{
    protected $bookModel;

    public function __construct()
    {
        // Hubungkan ke Database via Model
        $this->bookModel = new BookModel();
    }

    public function dashboard()
    {
        return view('Member/Dashboard');
    }

    // --- HALAMAN CARI BUKU (DIPERBAIKI) ---
    public function books()
    {
        // 1. Ambil keyword dari search bar
        $keyword = $this->request->getGet('keyword');

        // 2. Ambil data dari DATABASE (Bukan API port 8000)
        if ($keyword) {
            $data['books'] = $this->bookModel->search($keyword)->findAll();
        } else {
            $data['books'] = $this->bookModel->findAll();
        }

        $data['keyword'] = $keyword;

        // 3. Tampilkan View
        return view('Member/books/index', $data);
    }

    // --- HALAMAN DETAIL BUKU ---
    public function detail($id)
    {
        // Cari buku di database berdasarkan ID
        $book = $this->bookModel->find($id);

        if ($book) {
            $data['book'] = $book;
            return view('Member/books/detail', $data);
        } else {
            return redirect()->to('/member/books');
        }
    }
}
