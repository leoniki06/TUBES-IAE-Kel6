<?php

function apiRequest($method, $url, $token = null)
{
    $client = \Config\Services::curlrequest();

    $headers = ['Accept' => 'application/json'];

    if ($token) {
        $headers['Authorization'] = 'Bearer ' . $token;
    }

    $response = $client->request($method, $url, [
        'headers' => $headers
    ]);

    return json_decode($response->getBody(), true);
}
