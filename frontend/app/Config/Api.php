<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Api extends BaseConfig
{
    public string $ping     = '/api/ping';
    public string $login    = '/api/auth/login';
    public string $register = '/api/auth/register';
    public string $me       = '/api/auth/me';
    public string $logout   = '/api/auth/logout';
}
