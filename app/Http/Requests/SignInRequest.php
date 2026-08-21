<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SignInRequest extends FormRequest
{

    public function attributes() : array {
        return [
            'password' => 'contraseña'
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'No encontramos una cuenta con ese correo electronico'
        ];
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required']
        ];
    }
}
