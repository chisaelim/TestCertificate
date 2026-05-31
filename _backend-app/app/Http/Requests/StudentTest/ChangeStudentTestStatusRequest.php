<?php

namespace App\Http\Requests\StudentTest;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStudentTestStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:student_tests,id'],
            'status' => ['required', 'string', 'in:PENDING,PASSED,FAILED'],
        ];
    }
}
