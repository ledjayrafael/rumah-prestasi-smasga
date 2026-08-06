<?php

namespace App\Http\Requests;

use App\Enums\AchievementCategory;
use App\Enums\AchievementLevel;
use App\Enums\ParticipationType;
use App\Models\Achievement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class UpdateAchievementRequest extends FormRequest
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
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => [
                'nullable',
                File::types(['jpg', 'jpeg', 'png', 'pdf'])->max(10240),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var Achievement $achievement */
            $achievement = $this->route('achievement');
            $uploaded = array_filter($this->file('files', []) ?? []);

            if ($uploaded === [] && $achievement->files()->count() === 0) {
                $validator->errors()->add('files', 'Unggah minimal satu berkas bukti prestasi.');
            }
        });
    }
}
