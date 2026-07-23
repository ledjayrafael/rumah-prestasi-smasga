<?php

namespace App\Http\Requests\Admin;

use App\Enums\AchievementCategory;
use App\Enums\AchievementLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCompetitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', new Enum(AchievementCategory::class)],
            'level' => ['required', new Enum(AchievementLevel::class)],
            'organizer' => ['required', 'string', 'max:255'],
            'registration_deadline' => ['required', 'date'],
            'registration_url' => ['nullable', 'url', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
