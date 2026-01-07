<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Api extends BaseConfig
{

    public string $baseURL = 'http://127.0.0.1:9000';

    public string $ping     = '/api/ping';
    public string $login    = '/api/auth/login';
    public string $register = '/api/auth/register';
    public string $me       = '/api/auth/me';
    public string $logout   = '/api/auth/logout';
}
