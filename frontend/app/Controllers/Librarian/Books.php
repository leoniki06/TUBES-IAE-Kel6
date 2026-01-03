<?php

namespace App\Controllers\Librarian;

use App\Controllers\BaseController;
use App\Libraries\ApiClient;
use CodeIgniter\Exceptions\PageNotFoundException;

class Books extends BaseController
{
    private ApiClient $api;

    public function __construct()
    {
        $this->api = new ApiClient();
    }

    public function index()
    {
        $page     = (int) ($this->request->getGet('page') ?? 1);
        $search   = trim((string) ($this->request->getGet('search') ?? ''));
        $category = trim((string) ($this->request->getGet('category') ?? ''));

        // NOTE:
        // Ini butuh ApiClient get() support options 'query'.
        // Kalau ApiClient kamu belum support, sementara bisa pakai query string manual (lihat catatan bawah).
        $res = $this->api->get('/books', [
            'query' => [
                'page'     => max(1, $page),
                'search'   => $search !== '' ? $search : null,
                'category' => $category !== '' ? $category : null,
            ],
        ]);

        $payload = $res['data'] ?? [];

        $books = $payload['data'] ?? (is_array($payload) ? $payload : []);
        $meta  = $payload['meta'] ?? [
            'current_page' => max(1, $page),
            'last_page'    => 1,
            'total'        => is_array($books) ? count($books) : 0,
        ];

        return view('librarian/Books/index', [
            'title'    => 'Books',
            'books'    => $books,
            'meta'     => $meta,
            'search'   => $search,
            'category' => $category,
            'basePath' => 'librarian/books',
        ]);
    }

    public function show(int $id)
    {
        $res = $this->api->get("/books/{$id}");

        if (($res['status'] ?? 200) === 404) {
            throw PageNotFoundException::forPageNotFound("Book #{$id} not found");
        }

        $payload = $res['data'] ?? [];
        $book    = $payload['data'] ?? $payload;

        return view('librarian/Books/show', [
            'title'    => 'Book Detail',
            'book'     => $book,
            'basePath' => 'librarian/books',
        ]);
    }

    public function create()
    {
        return view('librarian/Books/form', [
            'title'    => 'Add Book',
            'mode'     => 'create',
            'book'     => [
                'title'       => '',
                'author'      => '',
                'isbn'        => '',
                'category'    => '',
                'publisher'   => '',
                'year'        => '',
                'stock'       => 1,
                'description' => '',
            ],
            'errors'   => session()->getFlashdata('errors') ?? [],
            'old'      => session()->getFlashdata('old') ?? [],
            'basePath' => 'librarian/books',
        ]);
    }

    public function store()
    {
        $payload = $this->collectPayload();
        $errors  = $this->validatePayload($payload);

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors)->with('old', $payload);
        }

        $res = $this->api->post('/books', $payload);

        if (($res['status'] ?? 201) >= 400) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $res['data']['errors'] ?? ['api' => ($res['data']['message'] ?? 'Gagal membuat buku')]);
        }

        return redirect()->to(base_url('librarian/books'))->with('success', 'Book created');
    }

    public function edit(int $id)
    {
        $res = $this->api->get("/books/{$id}");

        if (($res['status'] ?? 200) === 404) {
            throw PageNotFoundException::forPageNotFound("Book #{$id} not found");
        }

        $payload = $res['data'] ?? [];
        $book    = $payload['data'] ?? $payload;

        return view('librarian/Books/form', [
            'title'    => 'Edit Book',
            'mode'     => 'edit',
            'book'     => $book,
            'errors'   => session()->getFlashdata('errors') ?? [],
            'old'      => session()->getFlashdata('old') ?? [],
            'basePath' => 'librarian/books',
        ]);
    }

    public function update(int $id)
    {
        $payload = $this->collectPayload();
        $errors  = $this->validatePayload($payload);

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors)->with('old', $payload);
        }

        $res = $this->api->put("/books/{$id}", $payload);

        if (($res['status'] ?? 200) >= 400) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $res['data']['errors'] ?? ['api' => ($res['data']['message'] ?? 'Gagal update buku')]);
        }

        return redirect()->to(base_url("librarian/books/{$id}"))->with('success', 'Book updated');
    }

    public function destroy(int $id)
    {
        $res = $this->api->delete("/books/{$id}");

        if (($res['status'] ?? 200) >= 400) {
            return redirect()->to(base_url('librarian/books'))
                ->with('error', $res['data']['message'] ?? 'Gagal delete buku');
        }

        return redirect()->to(base_url('librarian/books'))->with('success', 'Book deleted');
    }

    private function collectPayload(): array
    {
        return [
            'title'       => trim((string) $this->request->getPost('title')),
            'author'      => trim((string) $this->request->getPost('author')),
            'isbn'        => trim((string) $this->request->getPost('isbn')),
            'category'    => trim((string) $this->request->getPost('category')),
            'publisher'   => trim((string) $this->request->getPost('publisher')),
            'year'        => (int) ($this->request->getPost('year') ?: 0),
            'stock'       => (int) ($this->request->getPost('stock') ?: 0),
            'description' => trim((string) $this->request->getPost('description')),
        ];
    }

    private function validatePayload(array $p): array
    {
        $e = [];
        if ($p['title'] === '') $e['title'] = 'Title wajib diisi';
        if ($p['author'] === '') $e['author'] = 'Author wajib diisi';
        if ($p['category'] === '') $e['category'] = 'Category wajib diisi';
        if ($p['stock'] < 0) $e['stock'] = 'Stock tidak boleh negatif';
        if ($p['isbn'] !== '' && strlen($p['isbn']) < 10) $e['isbn'] = 'ISBN minimal 10 karakter';
        return $e;
    }
}
