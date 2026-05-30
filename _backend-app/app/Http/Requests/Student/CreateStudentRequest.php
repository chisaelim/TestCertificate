<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_kh' => ['required', 'string', 'khmer_only'],
            'name_en' => ['required', 'string', 'english_only'],
            'dob' => ['required', 'date_format:d-m-Y'],
            'job' => ['required', 'string'],
            'home_no' => ['nullable', 'string'],
            'street_no' => ['nullable', 'string'],
            'phone' => ['required', 'regex:/^0[0-9]{2}[0-9]{3}[0-9]{3,4}$/'],
            'image' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg', 'dimensions:width=454,height=454'],

            'id_gender' => ['required', 'integer', 'exists:tbl_gender,id_gender'],
            'id_ethnicity' => ['required', 'integer', 'exists:tbl_ethnicity,id_ethnicity'],
            'id_nationality' => ['required', 'integer', 'exists:tbl_nationality,id_nationality'],
            'id_religion' => ['required', 'integer', 'exists:tbl_religion,id_religion'],

            'id_pob_province' => ['required', 'integer', 'exists:tbl_geography,id_geography'],
            'id_pob_district' => ['nullable', 'integer', 'exists:tbl_geography,id_geography'],
            'id_pob_commune' => ['nullable', 'integer', 'exists:tbl_geography,id_geography'],
            'id_pob_village' => ['nullable', 'integer', 'exists:tbl_geography,id_geography'],

            'id_por_province' => ['required', 'integer', 'exists:tbl_geography,id_geography'],
            'id_por_district' => ['nullable', 'integer', 'exists:tbl_geography,id_geography'],
            'id_por_commune' => ['nullable', 'integer', 'exists:tbl_geography,id_geography'],
            'id_por_village' => ['nullable', 'integer', 'exists:tbl_geography,id_geography'],
        ];
    }
}
