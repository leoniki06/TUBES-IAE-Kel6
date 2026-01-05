<?php

namespace App\Controllers\Librarian;

use App\Controllers\BaseController;
use App\Libraries\ApiClient;

class Books extends BaseController
{
    private ApiClient $api;

    public function __construct()
    {
        $this->api = new ApiClient();
        helper(['form', 'url']);
    }

    public function index()
    {
        if (!session('token')) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login dulu');
        }

        $search = trim((string) $this->request->getGet('search'));
        $page   = (int) ($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;

        $res = $this->api->get('/api/books', [
            'query' => array_filter([
                'search' => $search !== '' ? $search : null,
                'page'   => $page,
            ], fn($v) => $v !== null),
        ]);

        if (!$res['ok'] && (int) ($res['status'] ?? 0) === 401) {
            session()->remove(['token', 'user']);
            return redirect()->to('/auth/login')->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $payload = $res['data']['data'] ?? [];
        $books   = $payload['data'] ?? [];
        $meta    = $payload ?: [
            'current_page' => 1,
            'last_page'    => 1,
            'total'        => count($books),
        ];

        return view('librarian/books/index', [
            'books'      => $books,
            'meta'       => $meta,
            'search'     => $search,
            'totalItems' => (int)($meta['total'] ?? count($books)),
        ]);
    }

    public function store()
    {
        if (!session('token')) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login dulu');
        }

        $data = $this->request->getPost([
            'isbn',
            'title',
            'author',
            'publisher',
            'genre',
            'year',
            'stock_total',
            'stock_available',
        ]);

        $rules = [
            'isbn'            => 'required|min_length[3]',
            'title'           => 'required|min_length[2]',
            'author'          => 'required|min_length[2]',
            'genre'           => 'required|min_length[2]',
            'year'            => 'permit_empty|integer',
            'stock_total'     => 'required|integer|greater_than_equal_to[0]',
            'stock_available' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $total = (int) ($data['stock_total'] ?? 0);
        $avail = (int) ($data['stock_available'] ?? 0);

        // ✅ aturan yang kamu mau
        if ($avail > $total) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['stock_available' => 'Stock Available tidak boleh lebih besar dari Stock Total.']);
        }

        $payload = [
            'isbn'            => (string) $data['isbn'],
            'title'           => (string) $data['title'],
            'author'          => (string) $data['author'],
            'publisher'       => (string) ($data['publisher'] ?? ''),
            'genre'           => (string) ($data['genre'] ?? ''),
            'year'            => (int) ($data['year'] ?: 0),
            'stock_total'     => $total,
            'stock_available' => $avail,
        ];

        $res = $this->api->post('/api/books', ['form_params' => $payload]);

        if (!$res['ok']) {
            $msg = $res['data']['message'] ?? 'Gagal tambah book (API error).';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        return redirect()->to(base_url('librarian/books'))->with('success', 'Book berhasil ditambahkan.');
    }

    public function update($id)
    {
        if (!session('token')) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login dulu');
        }

        $id = (int) $id;

        $data = $this->request->getPost([
            'isbn',
            'title',
            'author',
            'publisher',
            'genre',
            'year',
            'stock_total',
            'stock_available',
        ]);

        $rules = [
            'isbn'            => 'required|min_length[3]',
            'title'           => 'required|min_length[2]',
            'author'          => 'required|min_length[2]',
            'genre'           => 'required|min_length[2]',
            'year'            => 'permit_empty|integer',
            'stock_total'     => 'required|integer|greater_than_equal_to[0]',
            'stock_available' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $total = (int) ($data['stock_total'] ?? 0);
        $avail = (int) ($data['stock_available'] ?? 0);

        // ✅ FIX: sebelumnya belum ada check ini di update (ini yang bikin kamu bingung)
        if ($avail > $total) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['stock_available' => 'Stock Available tidak boleh lebih besar dari Stock Total.']);
        }

        $payload = [
            'isbn'            => (string) $data['isbn'],
            'title'           => (string) $data['title'],
            'author'          => (string) $data['author'],
            'publisher'       => (string) ($data['publisher'] ?? ''),
            'genre'           => (string) ($data['genre'] ?? ''),
            'year'            => (int) ($data['year'] ?: 0),
            'stock_total'     => $total,
            'stock_available' => $avail,
        ];

        $res = $this->api->put("/api/books/{$id}", ['form_params' => $payload]);

        if (!$res['ok']) {
            $msg = $res['data']['message'] ?? 'Gagal update book (API error).';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        return redirect()->to(base_url('librarian/books'))->with('success', 'Book berhasil diupdate.');
    }

    public function delete($id)
    {
        if (!session('token')) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login dulu');
        }

        $id = (int) $id;

        $res = $this->api->delete("/api/books/{$id}");

        if (!$res['ok']) {
            $msg = $res['data']['message'] ?? 'Gagal hapus book (API error).';
            return redirect()->back()->with('error', $msg);
        }

        return redirect()->to(base_url('librarian/books'))->with('success', 'Book berhasil dihapus.');
    }
}
