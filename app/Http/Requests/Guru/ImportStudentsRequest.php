<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class ImportStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isWaliKelas() ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls', 'max:2048'],
        ];
    }
}
