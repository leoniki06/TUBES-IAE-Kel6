<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogAuthApi
{
    public function handle(Request $request, Closure $next)
    {
        if (str_starts_with($request->path(), 'api/auth')) {
            Log::info('CI4->LARAVEL AUTH HIT', [
                'method' => $request->method(),
                'path'   => $request->path(),
                'full'   => $request->fullUrl(),
                'ip'     => $request->ip(),
                'accept' => $request->header('accept'),
                'ctype'  => $request->header('content-type'),
                'ua'     => $request->header('user-agent'),
                'body'   => $request->all(),
            ]);
        }

        return $next($request);
    }
}
