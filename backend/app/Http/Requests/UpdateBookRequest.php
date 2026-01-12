<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'isbn'            => ['nullable', 'string', 'max:30'],
            'title'           => ['required', 'string', 'max:200'],
            'author'          => ['required', 'string', 'max:150'],
            'publisher'       => ['nullable', 'string', 'max:150'],
            'genre'           => ['required', 'string', 'max:80'],
            'year'            => ['nullable', 'integer', 'min:0', 'max:3000'],
            'stock_total'     => ['required', 'integer', 'min:0'],
            'stock_available' => ['nullable', 'integer', 'min:0'],

            // ✅ cover optional
            'cover'           => ['nullable','file','mimes:jpg,jpeg,png,webp','max:2048'],
            // ✅ optional hapus cover tanpa upload baru
            'remove_cover'    => ['nullable','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'isbn'            => $this->input('isbn') !== null ? trim((string) $this->input('isbn')) : null,
            'title'           => trim((string) $this->input('title')),
            'author'          => trim((string) $this->input('author')),
            'publisher'       => $this->input('publisher') !== null ? trim((string) $this->input('publisher')) : null,
            'genre'           => $this->input('genre') !== null ? trim((string) $this->input('genre')) : null,
            'year'            => $this->input('year') !== null && $this->input('year') !== '' ? (int) $this->input('year') : null,
            'stock_total'     => $this->input('stock_total') !== null ? (int) $this->input('stock_total') : null,
            'stock_available' => $this->input('stock_available') !== null && $this->input('stock_available') !== '' ? (int) $this->input('stock_available') : null,

            'remove_cover'    => $this->input('remove_cover') !== null ? (bool) $this->input('remove_cover') : null,
        ]);
    }
}
