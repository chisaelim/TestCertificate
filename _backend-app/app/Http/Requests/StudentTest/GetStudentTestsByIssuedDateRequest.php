<?php

namespace App\Http\Requests\StudentTest;

use Illuminate\Foundation\Http\FormRequest;

class GetStudentTestsByIssuedDateRequest extends FormRequest
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
