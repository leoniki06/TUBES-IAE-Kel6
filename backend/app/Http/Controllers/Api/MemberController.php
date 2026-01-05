<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * LIST MEMBER
     * GET /api/members?search=&page=&per_page=
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('search', ''));

        $perPage = (int) $request->query('per_page', 10);
        if ($perPage < 1) $perPage = 10;
        if ($perPage > 50) $perPage = 50;

        // optional: escape wildcard biar search tidak “meledak”
        $qLike = $q !== '' ? addcslashes($q, '%_\\') : '';

        $members = User::query()
            ->where('role', 'member')
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'address',
                'role',
                'is_active',
                'created_at',
                'updated_at',
            ])
            ->when($qLike !== '', function ($query) use ($qLike) {
                $query->where(function ($qq) use ($qLike) {
                    $qq->where('name', 'like', "%{$qLike}%")
                        ->orWhere('email', 'like', "%{$qLike}%")
                        ->orWhere('phone', 'like', "%{$qLike}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $members,
        ]);
    }

    public function store(StoreMemberRequest $request)
    {
        $payload = $request->validated();

        $member = User::create([
            'name'      => $payload['name'],
            'email'     => $payload['email'],
            'password'  => Hash::make($payload['password']),
            'role'      => 'member',
            'is_active' => array_key_exists('is_active', $payload) ? (bool) $payload['is_active'] : true,
            'phone'     => $payload['phone'] ?? null,
            'address'   => $payload['address'] ?? null,
        ]);

        // pastikan response gak bocorin field sensitif
        $member->makeHidden(['password', 'remember_token']);

        return response()->json([
            'success' => true,
            'message' => 'Member created',
            'data'    => $member,
        ], 201);
    }

    public function show(User $member)
    {
        if (($member->role ?? null) !== 'member') {
            return response()->json([
                'success' => false,
                'message' => 'Not found',
            ], 404);
        }

        $member->makeHidden(['password', 'remember_token']);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $member,
        ]);
    }

    public function update(UpdateMemberRequest $request, User $member)
    {
        if (($member->role ?? null) !== 'member') {
            return response()->json([
                'success' => false,
                'message' => 'Not found',
            ], 404);
        }

        $payload = $request->validated();

        $member->name    = $payload['name'];
        $member->email   = $payload['email'];
        $member->phone   = $payload['phone'] ?? null;
        $member->address = $payload['address'] ?? null;

        /**
         * ✅ FIX utama:
         * - kalau is_active tidak dikirim, jangan ubah nilainya
         */
        if (array_key_exists('is_active', $payload)) {
            $member->is_active = (bool) $payload['is_active'];
        }

        /**
         * ✅ password optional
         */
        if (!empty($payload['password'])) {
            $member->password = Hash::make($payload['password']);
        }

        $member->save();
        $member->makeHidden(['password', 'remember_token']);

        return response()->json([
            'success' => true,
            'message' => 'Member updated',
            'data'    => $member,
        ]);
    }

    public function destroy(User $member)
    {
        if (($member->role ?? null) !== 'member') {
            return response()->json([
                'success' => false,
                'message' => 'Not found',
            ], 404);
        }

        $member->is_active = false;
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Member deactivated',
        ]);
    }
}
