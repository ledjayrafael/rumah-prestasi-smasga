<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isWaliKelas() ?? false;
    }

    public function rules(): array
    {
        return [
            'student_ids' => ['required', 'array', 'min:1', 'max:50'],
            'student_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}
