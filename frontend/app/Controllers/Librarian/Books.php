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
    }

    public function index()
    {
        $search = trim((string) $this->request->getGet('search'));
        $page   = (int) ($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;

        $res = $this->api->get('/api/books', [
            'query' => array_filter([
                'search'   => $search !== '' ? $search : null,
                'page'     => $page,
                'per_page' => 10,
            ])
        ]);

        if (!($res['ok'] ?? false) || !is_array($res['data'] ?? null)) {
            return view('librarian/books/index', [
                'books' => [],
                'meta'  => ['current_page' => 1, 'last_page' => 1, 'total' => 0],
                'search'=> $search,
                'error' => 'Gagal akses API books. HTTP ' . ($res['status'] ?? 0),
            ]);
        }

        $payload = $res['data'] ?? [];
        $data    = $payload['data'] ?? [];

        $books = $data['data'] ?? [];
        $meta  = [
            'current_page' => (int) ($data['current_page'] ?? 1),
            'last_page'    => (int) ($data['last_page'] ?? 1),
            'total'        => (int) ($data['total'] ?? count($books)),
        ];

        return view('librarian/books/index', [
            'books'      => $books,
            'meta'       => $meta,
            'totalItems' => $meta['total'],
            'search'     => $search,
        ]);
    }

    /**
     * ==========================
     * CREATE (POST)
     * ==========================
     */
    public function store()
    {
        [$fields, $file, $hasCover] = $this->collectFieldsAndCover();

        // tanpa cover => form_params (lebih stabil)
        if (!$hasCover) {
            $res = $this->api->post('/api/books', [
                'form_params' => $fields,
            ]);
            return $this->handleWriteResult($res, '/librarian/books', 'Buku berhasil ditambahkan.', true);
        }

        // dengan cover => multipart
        $multipart = $this->toMultipart($fields, $file);
        $res = $this->api->post('/api/books', [
            'multipart' => $multipart,
        ]);

        return $this->handleWriteResult($res, '/librarian/books', 'Buku berhasil ditambahkan.', true);
    }

    /**
     * ==========================
     * UPDATE (PUT)
     * ==========================
     */
    public function update($id)
    {
        [$fields, $file, $hasCover] = $this->collectFieldsAndCover();

        // remove cover checkbox
        if (!empty($this->request->getPost('remove_cover'))) {
            $fields['remove_cover'] = '1';
        }

        // tanpa cover => form_params (PUT + urlencoded via ApiClient fix)
        if (!$hasCover) {
            $res = $this->api->put("/api/books/{$id}", [
                'form_params' => $fields,
            ]);
            return $this->handleWriteResult($res, '/librarian/books', 'Buku berhasil diupdate.', false);
        }

        // dengan cover => multipart
        $multipart = $this->toMultipart($fields, $file);
        $res = $this->api->put("/api/books/{$id}", [
            'multipart' => $multipart,
        ]);

        return $this->handleWriteResult($res, '/librarian/books', 'Buku berhasil diupdate.', false);
    }

    /**
     * ==========================
     * DELETE
     * ==========================
     */
    public function delete($id)
    {
        $res = $this->api->delete("/api/books/{$id}");
        return $this->handleWriteResult($res, '/librarian/books', 'Buku berhasil dihapus.', false);
    }

    /**
     * ==========================
     * HELPERS
     * ==========================
     */

    /**
     * Ambil semua field dari form + file cover (kalau ada)
     */
    private function collectFieldsAndCover(): array
    {
        $fields = [
            'isbn'            => trim((string)$this->request->getPost('isbn')),
            'title'           => trim((string)$this->request->getPost('title')),
            'author'          => trim((string)$this->request->getPost('author')),
            'publisher'       => trim((string)$this->request->getPost('publisher')),
            'genre'           => trim((string)$this->request->getPost('genre')),
            'year'            => trim((string)$this->request->getPost('year')),
            'stock_total'     => trim((string)$this->request->getPost('stock_total')),
            'stock_available' => trim((string)$this->request->getPost('stock_available')),
        ];

        $file = $this->request->getFile('cover');
        $hasCover = ($file && $file->isValid() && !$file->hasMoved());

        return [$fields, $file, $hasCover];
    }

    /**
     * Convert associative array fields + file menjadi multipart array
     */
    private function toMultipart(array $fields, $file): array
    {
        $multipart = [];
        foreach ($fields as $k => $v) {
            $multipart[] = ['name' => $k, 'contents' => $v];
        }

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $multipart[] = [
                'name'     => 'cover',
                'contents' => fopen($file->getTempName(), 'r'),
                'filename' => $file->getName(),
            ];
        }

        return $multipart;
    }

    /**
     * Flatten errors dari Laravel (422)
     */
    private function flattenLaravelErrors(array $payload): array
    {
        $out = [];

        if (isset($payload['errors']) && is_array($payload['errors'])) {
            foreach ($payload['errors'] as $field => $msgs) {
                if (is_array($msgs)) {
                    foreach ($msgs as $m) $out[] = (string)$m;
                } else {
                    $out[] = (string)$msgs;
                }
            }
        }

        return $out;
    }

    /**
     * Handler universal untuk store/update/delete
     *
     * $openAddModalOnError:
     * - true untuk store => supaya modal add kebuka lagi saat error
     * - false untuk update/delete
     */
    private function handleWriteResult(array $res, string $redirectTo, string $successMsg, bool $openAddModalOnError)
    {
        $payload = $res['data'] ?? [];

        // gagal HTTP (termasuk 422)
        if (!($res['ok'] ?? false)) {
            $errors = $this->flattenLaravelErrors($payload);

            $redir = redirect()->to($redirectTo)
                ->with('error', $payload['message'] ?? 'Validation error')
                ->withInput();

            if (!empty($errors)) $redir = $redir->with('errors', $errors);

            // trigger modal add kebuka lagi
            if ($openAddModalOnError) {
                $redir = $redir->with('open_add_modal', true);
            }

            return $redir;
        }

        // ok tapi success false (kalau backend kamu pakai flag)
        if (isset($payload['success']) && $payload['success'] === false) {
            $errors = $this->flattenLaravelErrors($payload);

            $redir = redirect()->to($redirectTo)
                ->with('error', $payload['message'] ?? 'Gagal memproses data.')
                ->withInput();

            if (!empty($errors)) $redir = $redir->with('errors', $errors);

            if ($openAddModalOnError) {
                $redir = $redir->with('open_add_modal', true);
            }

            return $redir;
        }

        return redirect()->to($redirectTo)->with('success', $successMsg);
    }
}
