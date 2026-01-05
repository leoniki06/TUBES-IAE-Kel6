<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'member_id',
        'book_id',
        'processed_by',
        'status',
        'qty',
        'requested_at',
        'approved_at',
        'borrowed_at',
        'due_at',
        'returned_at',
        'note',
    ];

    protected $casts = [
        'requested_at' => 'date',
        'approved_at'  => 'date',
        'borrowed_at'  => 'date',
        'due_at'       => 'date',
        'returned_at'  => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function librarian()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
