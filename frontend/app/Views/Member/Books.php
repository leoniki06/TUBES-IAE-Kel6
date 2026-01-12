<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Libraries\ApiClient;

class Books extends BaseController
{
    private ApiClient $api;

    public function __construct()
    {
        $this->api = new ApiClient();
    }

    public function index()
    {
        $keyword = trim((string) $this->request->getGet('keyword'));

        // ambil list buku
        $res = $this->api->get('/api/books', [
            'query' => array_filter([
                'search' => $keyword !== '' ? $keyword : null,
            ])
        ]);

        $payload = $res['data'] ?? [];
        $books   = $payload['data'] ?? ($payload['data']['data'] ?? []);

        // normalize cover list dari lokal (assets/img/books)
        foreach ($books as &$b) {
            $b['cover'] = $this->resolveLocalCover($b);
        }

        return view('Member/books/cari_buku', [
            'books'   => $books,
            'keyword' => $keyword,
        ]);
    }

    public function detail($id)
    {
        // ambil detail buku dari API
        $res = $this->api->get("/api/books/{$id}");
        $payload = $res['data'] ?? [];

        // asumsi API mengirim { success, data }
        $book = $payload['data'] ?? null;

        // kalau API kamu formatnya {data:{...}} tanpa success, ini tetap aman
        if (!$book || !is_array($book)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Buku tidak ditemukan');
        }

        // === INI FIX UTAMANYA ===
        // inject cover lokal walaupun API detail tidak kirim cover
        $book['cover'] = $this->resolveLocalCover($book);

        return view('Member/books/detail', [
            'book' => $book
        ]);
    }

    private function resolveLocalCover(array $book): ?string
    {
        // kalau API sudah kasih cover, boleh dipakai langsung (nama file atau url)
        $cover = $book['cover'] ?? $book['image'] ?? $book['thumbnail'] ?? null;
        if (is_string($cover) && trim($cover) !== '') {
            return trim($cover);
        }

        $title = (string)($book['title'] ?? $book['judul'] ?? '');
        if ($title === '') return null;

        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');

        // cek beberapa ekstensi
        $candidates = [
            $slug . '.jpg',
            $slug . '.jpeg',
            $slug . '.png',
            $slug . '.webp',
        ];

        foreach ($candidates as $file) {
            if (file_exists(FCPATH . 'assets/img/books/' . $file)) {
                return $file; // contoh: laskar-pelangi.jpg
            }
        }

        return null;
    }
}
