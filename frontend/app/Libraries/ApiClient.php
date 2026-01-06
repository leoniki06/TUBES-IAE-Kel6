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

        $this->baseUrl = rtrim($env, '/');

        // fallback untuk local dev
        if ($this->baseUrl === '') {
            $this->baseUrl = 'http://127.0.0.1:9000';
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

    private function fullUrl(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
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

    private function request(string $method, string $path, array $options = []): array
    {
        $url = $this->fullUrl($path);

        $client = Services::curlrequest([
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
            $resp   = $client->request($method, $url, $options);
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
                'data'   => $data,     // array|null
                'raw'    => $raw,      // simpan body asli untuk debug
                'error'  => null,
                'url'    => $url,
                'method' => $method,
            ];
        } catch (\Throwable $e) {
            return [
                'ok'     => false,
                'status' => 0,
                'data'   => null,
                'raw'    => null,
                'error'  => $e->getMessage(),
                'url'    => $url,
                'method' => $method,
            ];
        }
    }
}
