<?php

namespace App\Libraries;

use Config\Services;

class ApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        $env = (string) env('BACKEND_API_BASEURL');
        $this->baseUrl = rtrim($env, '/');

        // ✅ kamu bilang laravel jalan di 9000
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

    private function request(string $method, string $path, array $options = []): array
    {
        $url = $this->fullUrl($path);

        $client = Services::curlrequest([
            'timeout'     => 20,
            'http_errors' => false,
            'verify'      => false,
        ]);

        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent'   => 'CI4-Frontend',
        ];

        $token = session()->get('token');
        if (!empty($token)) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        if (!empty($options['headers']) && is_array($options['headers'])) {
            $headers = array_merge($headers, $options['headers']);
        }

        $options['headers'] = $headers;

        try {
            $resp = $client->request($method, $url, $options);
            $status = (int) $resp->getStatusCode();
            $body   = (string) $resp->getBody();

            $data = null;
            if ($body !== '') {
                $decoded = json_decode($body, true);
                $data = is_array($decoded) ? $decoded : ['raw' => $body];
            }

            return [
                'ok'     => $status >= 200 && $status < 300,
                'status' => $status,
                'data'   => $data,
                'error'  => null,
            ];
        } catch (\Throwable $e) {
            return [
                'ok'     => false,
                'status' => 0,
                'data'   => null,
                'error'  => $e->getMessage(),
            ];
        }
    }
}
