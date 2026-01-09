<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\BookModel;

class BookController extends BaseController
{
    protected $bookModel;

    public function __construct()
    {
        // Pastikan Anda sudah punya file app/Models/BookModel.php
        $this->bookModel = new BookModel();
    }

    // HALAMAN UTAMA (LIST & SEARCH)
    public function index()
    {
        // 1. Ambil keyword dari form HTML (name="keyword")
        $keyword = $this->request->getGet('keyword');

        // 2. Logika Pencarian
        if ($keyword) {
            // Group start/end agar logika OR tidak merusak filter lain
            $this->bookModel->groupStart()
                ->like('title', $keyword)
                ->orLike('author', $keyword)
                ->orLike('genre', $keyword) // Asumsi kolom di DB namanya 'genre' atau 'category'
                ->groupEnd();
        }

        // Ambil data buku (bisa ditambah ->where('stock >', 0) jika mau hanya yg ada stok)
        $books = $this->bookModel->findAll();

        $data = [
            'books'   => $books,
            'keyword' => $keyword // Kirim balik keyword agar input search tidak kosong setelah submit
        ];

        // Pastikan nama file view sesuai lokasi Anda (cari_buku.php)
        return view('Member/books/cari_buku', $data);
    }

    // HALAMAN DETAIL
    public function detail($id)
    {
        // 1. Cari buku berdasarkan ID
        $book = $this->bookModel->find($id);

        // 2. Jika buku tidak ditemukan, lempar 404
        if (!$book) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Buku dengan ID $id tidak ditemukan.");
        }

        // 3. PENTING: Load view 'detail', BUKAN 'cari_buku'
        // Pastikan file view ada di: app/Views/Member/books/detail.php
        return view('Member/books/detail', ['book' => $book]);
    }
}
