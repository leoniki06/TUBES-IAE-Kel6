<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,

        'auth'  => \App\Filters\AuthFilter::class,
        'role'  => \App\Filters\RoleFilter::class,
        'admin' => \App\Filters\AdminFilter::class,
    ];

    // ✅ Jangan pakai forcehttps/pagecache di required.
    public array $required = [
        'before' => [],
        'after'  => [],
    ];

    // ✅ Kalau mau forcehttps, pasang hanya di production (opsional).
    public array $globals = [
        'before' => [
            // 'forcehttps', // aktifkan hanya kalau server benar-benar https
            // 'csrf',
        ],
        'after' => [
            // 'secureheaders',
            'performance',
            'toolbar',
        ],
    ];

    public array $methods = [];
    public array $filters = [];
}
