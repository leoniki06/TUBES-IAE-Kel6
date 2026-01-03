<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'book_id' => ['required','integer','exists:books,id'],
            'days' => ['nullable','integer','min:1','max:60'],
        ];
    }
}
