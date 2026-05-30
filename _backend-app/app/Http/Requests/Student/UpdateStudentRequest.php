<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:students,id'],
            'name_kh' => ['required', 'string', 'max:250'],
            'name_en' => ['required', 'string', 'max:250'],
            'dob' => ['required', 'date_format:d-m-Y'],
            'home_no' => ['nullable', 'string'],
            'street_no' => ['nullable', 'string'],
            'phone' => ['required', 'regex:/^0[0-9]{2}[0-9]{3}[0-9]{3,4}$/', 'unique:students,phone,' . $this->id],
            'photo' => ['sometimes', 'nullable', 'file', 'image', 'mimes:png,jpg,jpeg', 'dimensions:width=454,height=454'],

            'gender_id' => ['required', 'integer', 'exists:genders,id'],
            'ethnicity_id' => ['required', 'integer', 'exists:ethnicities,id'],
            'nationality_id' => ['required', 'integer', 'exists:nationalities,id'],
            'religion_id' => ['required', 'integer', 'exists:religions,id'],

            'pob_province_id' => ['required', 'integer', 'exists:geographies,id'],
            'pob_district_id' => ['nullable', 'integer', 'exists:geographies,id'],
            'pob_commune_id' => ['nullable', 'integer', 'exists:geographies,id'],
            'pob_village_id' => ['nullable', 'integer', 'exists:geographies,id'],

            'por_province_id' => ['required', 'integer', 'exists:geographies,id'],
            'por_district_id' => ['nullable', 'integer', 'exists:geographies,id'],
            'por_commune_id' => ['nullable', 'integer', 'exists:geographies,id'],
            'por_village_id' => ['nullable', 'integer', 'exists:geographies,id'],
        ];
    }
}
