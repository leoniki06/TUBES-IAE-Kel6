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
    /**
     * Filter Aliases
     */
    public array $aliases = [
        // bawaan CI
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,

        // custom filters (PENTING)
        'auth'  => \App\Filters\AuthFilter::class,
        'role'  => \App\Filters\RoleFilter::class,
        'admin' => \App\Filters\AdminFilter::class,
    ];

    /**
     * Special required filters
     */
    public array $required = [
        'before' => [
            'forcehttps',
            'pagecache',
        ],
        'after' => [
            'pagecache',
            'performance',
            'toolbar',
        ],
    ];

    /**
     * Global filters
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
        ],
        'after' => [
            // 'secureheaders',
        ],
    ];

    /**
     * Method-based filters
     */
    public array $methods = [];

    /**
     * Pattern-based filters
     */
    public array $filters = [];
}
