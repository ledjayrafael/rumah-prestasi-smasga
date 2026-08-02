<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isWaliKelas() ?? false;
    }

    public function rules(): array
    {
        $studentProfileId = $this->route('student')->studentProfile->id;
        $classIds = $this->user()->manageableClassIds()->all();

        return [
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:30', Rule::unique('student_profiles', 'nis')->ignore($studentProfileId)],
            'school_class_id' => ['required', Rule::in($classIds)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
