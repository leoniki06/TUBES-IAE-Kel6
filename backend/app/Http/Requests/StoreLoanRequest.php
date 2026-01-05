<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // role check di controller/middleware
    }

    public function rules(): array
    {
        return [
            'book_id' => ['required','integer','exists:books,id'],
            'qty'     => ['nullable','integer','min:1','max:5'],
            'note'    => ['nullable','string','max:1000'],
        ];
    }
}
