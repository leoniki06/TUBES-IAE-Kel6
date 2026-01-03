<?php

namespace App\GraphQL\Queries;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyTransactionQuery
{
    public function builder($root, array $args): Builder
    {
        $user = Auth::guard('api')->user();

        return Transaction::query()
            ->with(['book', 'user'])
            ->where('user_id', $user->id)
            ->orderByDesc('id');
    }
}
