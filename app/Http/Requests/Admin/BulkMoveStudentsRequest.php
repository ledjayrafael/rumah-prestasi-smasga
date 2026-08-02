<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkMoveStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'student_ids' => ['required', 'array', 'min:1', 'max:50'],
            'student_ids.*' => ['integer', 'exists:users,id'],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
        ];
    }
}
