<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        if (str_starts_with($request->path(), 'api/auth')) {
            Log::info('API AUTH HIT', [
                'method' => $request->method(),
                'path'   => $request->path(),
                'full'   => $request->fullUrl(),
                'ip'     => $request->ip(),
                'accept' => $request->header('accept'),
                'ctype'  => $request->header('content-type'),
                'origin' => $request->header('origin'),
                'body'   => $request->all(),
            ]);
        }

        return $next($request);
    }
}
