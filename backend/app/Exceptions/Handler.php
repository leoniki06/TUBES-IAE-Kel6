<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        //
    }

    public function render($request, Throwable $e)
    {
        $forceJson =
            $request->expectsJson()
            || $request->is('api/*')
            || $request->is('graphql')
            || $request->is('graphql/*');

        if ($forceJson) {

            // ✅ 401 (JWT / GraphQL without token)
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // ✅ 403 (policy/role)
            if ($e instanceof AuthorizationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                ], 403);
            }

            // ✅ 422
            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors(),
                ], 422);
            }

            // ✅ 404
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not found',
                ], 404);
            }

            // ✅ any HttpException (405, 429, etc)
            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $msg = $e->getMessage() ?: 'Error';

                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], $status);
            }

            // ✅ 500 fallback (biar ketahuan saat debug)
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Server error',
            ], 500);
        }

        return parent::render($request, $e);
    }
}
