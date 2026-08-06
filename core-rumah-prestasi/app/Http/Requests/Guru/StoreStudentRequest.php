<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isWaliKelas() ?? false;
    }

    public function rules(): array
    {
        $classIds = $this->user()->manageableClassIds()->all();

        return [
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:30', 'unique:student_profiles,nis'],
            'school_class_id' => ['required', Rule::in($classIds)],
        ];
    }
}
