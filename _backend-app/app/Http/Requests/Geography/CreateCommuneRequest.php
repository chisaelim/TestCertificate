<?php

namespace App\Http\Requests\Geography;

use Illuminate\Foundation\Http\FormRequest;

class CreateCommuneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_province' => ['required', 'integer', 'exists:tbl_geography,id_geography'],
            'id_district' => ['required', 'integer', 'exists:tbl_geography,id_geography'],
            'name_kh' => ['required', 'khmer_only'],
            'name_en' => ['required', 'english_only'],
            'name_latin' => ['required', 'english_only'],
        ];
    }
}
