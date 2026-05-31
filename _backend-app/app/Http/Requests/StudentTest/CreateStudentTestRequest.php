<?php

namespace App\Http\Requests\StudentTest;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudentTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'test_id' => ['required', 'integer', 'exists:tests,id'],
            'issued_date' => ['required', 'date_format:d-m-Y'],
        ];
    }
}
