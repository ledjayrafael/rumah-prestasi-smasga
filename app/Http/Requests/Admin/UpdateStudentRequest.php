<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $studentProfileId = $this->route('student')->studentProfile->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:30', Rule::unique('student_profiles', 'nis')->ignore($studentProfileId)],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
