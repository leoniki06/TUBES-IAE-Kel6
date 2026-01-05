<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => ['required','string','max:150'],
            'email'     => ['required','email','max:150','unique:users,email'],
            'password'  => ['required','string','min:6'],
            'phone'     => ['nullable','string','max:30'],
            'address'   => ['nullable','string','max:2000'],
            'is_active' => ['nullable','boolean'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
