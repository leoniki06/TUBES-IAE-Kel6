<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $role = strtolower((string) ($user->role ?? ''));

        $allowed = array_map(fn($r) => strtolower((string)$r), $roles);

        if (!in_array($role, $allowed, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Role not allowed.',
            ], 403);
        }

        return $next($request);
    }
}
