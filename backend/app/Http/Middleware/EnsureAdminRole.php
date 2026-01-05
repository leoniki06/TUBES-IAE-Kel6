<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $role = strtolower((string) ($user->role ?? ''));

        if (!$user || !in_array($role, ['librarian', 'admin'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Librarian only.',
            ], 403);
        }

        return $next($request);
    }
}
