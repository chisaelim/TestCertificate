<?php

namespace App\Http\Requests\StudentTest;

use Illuminate\Foundation\Http\FormRequest;

class GetStudentTestsByStudentIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
