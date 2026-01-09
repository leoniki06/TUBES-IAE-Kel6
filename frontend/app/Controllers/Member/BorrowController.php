<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\BookModel;

class BorrowController extends BaseController
{
    public function save()
    {
        // 1. Ambil ID Buku dari Form
        $bookId = $this->request->getPost('book_id');
        $userId = session()->get('user.id') ?? 1; // Contoh User ID (sesuaikan session)

        $bookModel = new BookModel();
        $book = $bookModel->find($bookId);

        // 2. Validasi Stok di Backend (Penting!)
        if (!$book || $book['stock_available'] <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok buku ini sudah habis.');
        }

        // 3. Proses Peminjaman (Simulasi)
        // Disini nanti Anda akan insert ke tabel 'transactions' atau 'loans'
        // Lalu kurangi stok buku.

        /* Contoh Logika Update Stok (Jika mau diaktifkan):
        $bookModel->update($bookId, [
            'stock_available' => $book['stock_available'] - 1
        ]);
        */

        // 4. Redirect Sukses
        // Kita arahkan ke halaman "Buku Dipinjam" atau kembali ke detail dengan pesan sukses
        return redirect()->to('/member/books')->with('success', 'Berhasil meminjam buku: ' . $book['title']);
    }
}
