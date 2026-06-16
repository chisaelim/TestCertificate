<?php

namespace App\Http\Requests\Test;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:tests,id'],
            'name_en' => ['required', 'string', Rule::unique('tests', 'name_en')->ignore($this->route('id'))],
            'name_kh' => ['required', 'string', Rule::unique('tests', 'name_kh')->ignore($this->route('id'))],
            'short_name' => ['required', 'string', Rule::unique('tests', 'short_name')->ignore($this->route('id'))],
        ];
    }
}
