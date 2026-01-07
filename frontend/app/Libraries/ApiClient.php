<?php

namespace App\Libraries;

use Config\Services;

class ApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        // Ambil dari ENV CI4 (file .env CI4)
        $env = (string) env('BACKEND_API_BASEURL');

        // Trim spasi & petik (kalau user nulis '...' atau "...")
        $env = trim($env);
        $env = trim($env, "\"'");

        $this->baseUrl = rtrim($env, '/');

        // fallback untuk local dev
        if ($this->baseUrl === '') {
            $this->baseUrl = 'http://127.0.0.1:9000';
        }

        // Anti dobel /api kalau env salah isi .../api
        if (str_ends_with($this->baseUrl, '/api')) {
            $this->baseUrl = substr($this->baseUrl, 0, -4);
            $this->baseUrl = rtrim($this->baseUrl, '/');
        }
    }

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

    private function buildHeaders(array $optionsHeaders = []): array
    {
        $headers = [
            'Accept'     => 'application/json',
            'User-Agent' => 'CI4-Frontend',
        ];

        // tempel token (JWT) kalau ada
        $token = session('token');
        if (!empty($token)) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        // merge dari pemanggil
        if (!empty($optionsHeaders) && is_array($optionsHeaders)) {
            $headers = array_merge($headers, $optionsHeaders);
        }

        return $headers;
    }

    private function normalizePath(string $path): string
    {
        // kita pakai baseURI, jadi di request gunakan path relatif (tanpa host)
        // tetap izinkan user ngirim "/api/auth/login" atau "api/auth/login"
        $path = '/' . ltrim($path, '/');

        // Anti dobel /api/api kalau ada yang salah kirim path
        if (str_starts_with($path, '/api/api/')) {
            $path = substr($path, 4); // hapus "/api"
        }

        return $path;
    }

    private function request(string $method, string $path, array $options = []): array
    {
        $path = $this->normalizePath($path);

        $client = Services::curlrequest([
            'baseURI'     => $this->baseUrl,
            'timeout'     => 25,
            'http_errors' => false,
            'verify'      => false, // dev only; kalau https production, ubah true
        ]);

        $options['headers'] = $this->buildHeaders($options['headers'] ?? []);

        $isSendingJsonBody = array_key_exists('json', $options) && is_array($options['json']);
        if ($isSendingJsonBody) {
            $options['headers']['Content-Type'] = 'application/json';
        }

        try {
            $resp   = $client->request($method, $path, $options);
            $status = (int) $resp->getStatusCode();
            $raw    = (string) $resp->getBody();

            // decode JSON kalau bisa
            $data = null;
            if ($raw !== '') {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data = $decoded;
                }
            }

            return [
                'ok'     => $status >= 200 && $status < 300,
                'status' => $status,
                'data'   => $data,
                'raw'    => $raw,
                'error'  => null,
                'url'    => rtrim($this->baseUrl, '/') . $path,
                'method' => $method,
            ];
        } catch (\Throwable $e) {
            return [
                'ok'     => false,
                'status' => 0,
                'data'   => null,
                'raw'    => null,
                'error'  => $e->getMessage(),
                'url'    => rtrim($this->baseUrl, '/') . $path,
                'method' => $method,
            ];
        }
    }
}
