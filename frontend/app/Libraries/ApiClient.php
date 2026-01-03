<?php

namespace App\Libraries;

use Config\Services;

class ApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) env('BACKEND_API_BASEURL'), '/');
        if ($this->baseUrl === '') {
            $this->baseUrl = 'http://127.0.0.1:8000';
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /* ===============================
     * HTTP WRAPPERS (PUBLIC)
     * =============================== */

    public function get(string $path, array $options = []): array
    {
        return $this->request('GET', $path, $options);
    }

    public function post(string $path, array $options = []): array
    {
        return $this->request('POST', $path, $options);
    }

    public function put(string $path, array $options = []): array
    {
        return $this->request('PUT', $path, $options);
    }

    public function delete(string $path, array $options = []): array
    {
        return $this->request('DELETE', $path, $options);
    }

    /* ===============================
     * CORE REQUEST HANDLER
     * =============================== */

    private function request(string $method, string $path, array $options = []): array
    {
        $path = '/' . ltrim($path, '/');
        $url  = $this->baseUrl . $path;

        $client = Services::curlrequest([
            'baseURI'     => $this->baseUrl . '/',
            'timeout'     => 20,
            'http_errors' => false,
        ]);

        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent'   => 'CI4-Frontend',
        ];

        // 🔐 Bearer token (kalau login)
        if ($token = session('token')) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        $payload = [
            'headers' => $headers,
        ];

        // query params (?page=1&search=...)
        if (!empty($options['query'])) {
            $payload['query'] = $options['query'];
        }

        // json body (POST / PUT)
        if (!empty($options['json'])) {
            $payload['json'] = $options['json'];
        }

        try {
            $res    = $client->request($method, ltrim($path, '/'), $payload);
            $status = $res->getStatusCode();
            $raw    = (string) $res->getBody();
            $data   = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $data = ['message' => $raw];
            }

            return [
                'ok'     => $status >= 200 && $status < 300,
                'status' => $status,
                'url'    => $url,
                'data'   => $data,
            ];
        } catch (\Throwable $e) {
            return [
                'ok'     => false,
                'status' => 0,
                'url'    => $url,
                'data'   => [
                    'message' => 'Gagal konek backend API',
                    'error'   => $e->getMessage(),
                ],
            ];
        }
    }
}
