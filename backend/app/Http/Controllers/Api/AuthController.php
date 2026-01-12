<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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
            'is_active' => 1,
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

        // kalau kolom is_active ada, paksa hanya user aktif yang boleh attempt
        if (Schema::hasColumn('users', 'is_active')) {
            $credentials['is_active'] = 1;
        }

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials / inactive account',
                'errors'  => (object)[],
            ], 401);
        }

        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Logged in',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
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
