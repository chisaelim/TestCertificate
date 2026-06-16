<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:250'],
            'email' => ['required', 'email', 'max:250', Rule::unique('users', 'email')->ignore($this->route('id'))],
            'level' => ['required', 'string', 'in:_DOCUMENT_CONTROLLER_'],
            'new_password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
