<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'isbn' => ['nullable','string','max:30'],
            'title' => ['required','string','max:200'],
            'author' => ['required','string','max:150'],
            'publisher' => ['nullable','string','max:150'],
            'year' => ['nullable','integer','min:1000','max:3000'],
            'stock_total' => ['required','integer','min:0','max:100000'],
        ];
    }
}
