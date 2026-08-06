<?php

namespace App\Http\Requests;

use App\Enums\AchievementCategory;
use App\Enums\AchievementLevel;
use App\Enums\ParticipationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;

class StoreAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSiswa() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', new Enum(AchievementCategory::class)],
            'level' => ['required', new Enum(AchievementLevel::class)],
            'participation_type' => ['required', new Enum(ParticipationType::class)],
            'rank_label' => ['required', 'string', 'max:100'],
            'organizer' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:2000'],
            'files' => ['required', 'array', 'min:1', 'max:5'],
            'files.*' => [
                'nullable',
                File::types(['jpg', 'jpeg', 'png', 'pdf'])->max(10240),
            ],
        ];
    }
}
