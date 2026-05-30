<?php

namespace App\Http\Requests\Geography;

use Illuminate\Foundation\Http\FormRequest;

class CreateProvinceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_kh' => ['required', 'khmer_only'],
            'name_en' => ['required', 'english_only'],
            'name_latin' => ['required', 'english_only'],
        ];
    }
}
