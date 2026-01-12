<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\BookModel;
use App\Models\TransactionModel;

class BookController extends BaseController
{
    protected BookModel $bookModel;

    public function __construct()
    {
        $this->bookModel = new BookModel();
    }

    /**
     * Mapping judul buku => nama file cover di public/assets/img/books/
     * (Sesuaikan kalau judul di DB/API beda huruf besar/kecil - pakai strtolower)
     */
    private function coverMap(): array
    {
        return [
            'laskar pelangi' => 'laskar-pelangi.jpg',
            'bumi manusia' => 'bumi-manusia.jpg',
            'ayat-ayat cinta' => 'ayat-ayat-cinta.jpg',
            "harry potter and the philosopher’s stone" => 'Harry_Potter_and_the_Philosopher_s_Stone_Book_Cover.jpg',
            "harry potter and the philosopher's stone" => 'Harry_Potter_and_the_Philosopher_s_Stone_Book_Cover.jpg',
            'the hobbit' => 'the-hobbit.jpg',
        ];
    }

    /**
     * Resolve cover URL:
     * 1) kalau ada cover dari API/DB berupa URL => pakai
     * 2) kalau cover berupa nama file lokal => cek file exists
     * 3) kalau kosong => mapping by title
     * 4) terakhir => null (biar view pakai avatar)
     */
    private function resolveCover(?string $title, $coverFromData = null): ?string
    {
        // 1) cover sudah URL
        if (is_string($coverFromData) && preg_match('#^https?://#i', $coverFromData)) {
            return $coverFromData;
        }

        // 2) cover sudah nama file lokal
        if (is_string($coverFromData) && $coverFromData !== '') {
            $path = FCPATH . 'assets/img/books/' . $coverFromData;
            if (file_exists($path)) {
                return base_url('assets/img/books/' . $coverFromData);
            }
        }

        // 3) mapping by title
        $t = strtolower(trim((string)$title));
        $map = $this->coverMap();

        if ($t !== '' && isset($map[$t])) {
            $file = $map[$t];
            $path = FCPATH . 'assets/img/books/' . $file;
            if (file_exists($path)) {
                return base_url('assets/img/books/' . $file);
            }
        }

        // 4) gagal semua
        return null;
    }

    // =========================
    // LIST / CARI BUKU
    // =========================
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('keyword'));

        // ambil dari API/model kamu (sesuaikan dengan BookModel kamu)
        // Asumsi BookModel sudah ada method getBooks($keyword)
        $books = $this->bookModel->getBooks($keyword);

        // inject cover URL untuk tampilan katalog
        if (!empty($books) && is_array($books)) {
            foreach ($books as &$b) {
                $b['cover_url'] = $this->resolveCover($b['title'] ?? null, $b['cover'] ?? null);
            }
            unset($b);
        }

        return view('Member/books/cari_buku', [
            'books' => $books,
            'keyword' => $keyword
        ]);
    }

    // =========================
    // DETAIL BUKU
    // =========================
    public function detail($id)
    {
        // Cara aman: ambil buku by id dari model (bukan $this->api)
        // Asumsi BookModel punya method getBookById($id)
        $book = $this->bookModel->getBookById($id);

        if (!$book) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Buku tidak ditemukan');
        }

        // inject cover URL untuk detail
        $book['cover_url'] = $this->resolveCover($book['title'] ?? null, $book['cover'] ?? null);

        return view('Member/books/detail', [
            'book' => $book
        ]);
    }

    public function borrow($bookId)
{
    $user = session('user');
    if (!$user || !isset($user['id'])) {
        return redirect()->to('/')->with('error', 'Silakan login dulu.');
    }

    $userId = (int) $user['id'];
    $bookId = (int) $bookId;

    // ambil buku
    $book = $this->bookModel->find($bookId);
    if (!$book) {
        return redirect()->back()->with('error', 'Buku tidak ditemukan.');
    }

    $available = (int)($book['stock_available'] ?? 0);
    if ($available <= 0) {
        return redirect()->back()->with('error', 'Stok buku habis.');
    }

    // cek apakah user masih pinjam buku ini dan belum dikembalikan
    $trxModel = new TransactionModel();
    $exists = $trxModel->where('user_id', $userId)
        ->where('book_id', $bookId)
        ->groupStart()
            ->where('return_date', null)
            ->orWhere('return_date', '')
        ->groupEnd()
        ->first();

    if ($exists) {
        return redirect()->to('member/borrowed')->with('error', 'Kamu masih meminjam buku ini.');
        }

        // tanggal pinjam + jatuh tempo (misal 7 hari)
        $borrowDate = date('Y-m-d');
        $dueDate    = date('Y-m-d', strtotime('+7 days'));

        // transaksi DB biar aman
        $db = \Config\Database::connect();
        $db->transStart();

        // insert ke transactions
        $trxModel->insert([
            'user_id'      => $userId,
            'book_id'      => $bookId,
            'borrow_date'  => $borrowDate,
            'due_date'     => $dueDate,
            'return_date'  => null,
            'fine_amount'  => 0,
            'status'       => 'borrowed',
        ]);

        // kurangi stok_available
        $this->bookModel->update($bookId, [
            'stock_available' => $available - 1
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal meminjam buku. Coba lagi.');
        }

        return redirect()->to('member/borrowed')->with('success', 'Berhasil meminjam buku!');
    }

}
