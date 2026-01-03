<?php

namespace App\Controllers;

use App\Libraries\ApiClient;

class Debug extends BaseController
{
    public function index()
    {
        $api = new ApiClient();

        $fingerprint = $api->get('/api/fingerprint');
        $ping        = $api->get('/api/ping');

        return $this->response->setJSON([
            'ci4_url' => site_url(),
            'backend_base' => $api->getBaseUrl(),
            'fingerprint' => $fingerprint,
            'ping' => $ping,
        ]);
    }
}
