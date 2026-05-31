<?php

namespace App\Http\Requests\Test;

use Illuminate\Foundation\Http\FormRequest;

class GetTestsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string'],
        ];
    }
}
