<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $classId = $this->route('class')?->id;

        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('school_classes', 'name')->ignore($classId)],
            'grade_level' => ['required', Rule::in(['X', 'XI', 'XII'])],
            'major' => ['nullable', 'string', 'max:50'],
            'homeroom_teacher_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', UserRole::Guru->value),
            ],
        ];
    }
}
