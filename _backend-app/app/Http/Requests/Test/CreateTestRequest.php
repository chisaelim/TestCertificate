<?php

namespace App\Http\Requests\Test;

use Illuminate\Foundation\Http\FormRequest;

class CreateTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en' => ['required', 'string', 'unique:tests,name_en'],
            'name_kh' => ['required', 'string', 'unique:tests,name_kh'],
            'short_name' => ['required', 'string', 'unique:tests,short_name'],
        ];
    }
}
