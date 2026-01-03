<?php

namespace App\GraphQL\Queries;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;

class BookQuery
{
    public function builder($root, array $args): Builder
    {
        $q = $args['q'] ?? null;

        return Book::query()
            ->when($q, function ($query) use ($q) {
                $query->where('title','like',"%{$q}%")
                    ->orWhere('author','like',"%{$q}%")
                    ->orWhere('isbn','like',"%{$q}%");
            })
            ->orderByDesc('id');
    }
}
