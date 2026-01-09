<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $payload = $request->validated();

        $user = User::create([
            'name'      => (string) $payload['name'],
            'email'     => (string) $payload['email'],
            'password'  => Hash::make((string) $payload['password']),
            'role'      => 'member',     
            'is_active' => true,
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Registered',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ],
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors'  => (object)[],
            ], 401);
        }

        $user = auth('api')->user();

        // optional: blokir kalau nonaktif
        if (isset($user->is_active) && !$user->is_active) {
            auth('api')->logout();
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated',
                'errors'  => (object)[],
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged in',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user, // ✅ CI4 bisa ambil role dari sini (tanpa /me pun bisa)
            ],
        ], 200);
    }

    public function me()
    {
        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => auth('api')->user(),
        ], 200);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out',
            'data' => (object)[],
        ], 200);
    }
}
