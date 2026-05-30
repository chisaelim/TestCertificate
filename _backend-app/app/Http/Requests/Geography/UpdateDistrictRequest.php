<?php

namespace App\Http\Requests\Geography;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_district' => ['required', 'integer', 'exists:tbl_geography,id_geography'],
            'id_province' => ['required', 'integer', 'exists:tbl_geography,id_geography'],
            'name_kh' => ['required', 'khmer_only'],
            'name_en' => ['required', 'english_only'],
            'name_latin' => ['required', 'english_only'],
            'unit_en' => ['required', 'string', 'in:District,Municipality'],
        ];
    }
}
