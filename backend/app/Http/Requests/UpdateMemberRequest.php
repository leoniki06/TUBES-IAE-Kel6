<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // ✅ Ambil parameter route {member}
        // Bisa berupa id (angka) atau bisa model binding (User object)
        $memberParam = $this->route('member');

        // ✅ Pastikan $memberId benar-benar angka
        $memberId = is_object($memberParam) ? ($memberParam->id ?? null) : $memberParam;
        $memberId = is_numeric($memberId) ? (int) $memberId : null;

        return [
            'name'      => ['required', 'string', 'max:150'],

            // ✅ Cara paling aman: pakai Rule::unique()->ignore($id)
            'email'     => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($memberId),
            ],

            // password optional (kosong = tidak ganti)
            'password'  => ['nullable', 'string', 'min:6'],

            'phone'     => ['nullable', 'string', 'max:30'],
            'address'   => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    // ✅ response validasi konsisten JSON
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
