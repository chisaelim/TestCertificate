<?php

namespace App\Http\Requests\StudentTest;

use Illuminate\Foundation\Http\FormRequest;

class GetPassedStudentTestsForCertificatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'passed_ids' => ['required', 'array'],
            'passed_ids.*' => ['required', 'integer', 'exists:student_tests,id'],
        ];
    }
}
