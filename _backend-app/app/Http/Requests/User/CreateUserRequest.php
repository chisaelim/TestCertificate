<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:250'],
            'email' => ['required', 'email', 'max:250', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'level' => ['required', 'string', 'in:_DOCUMENT_CONTROLLER_'],
        ];
    }
}
