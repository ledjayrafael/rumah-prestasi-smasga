<?php

namespace App\Models;

use App\Enums\AchievementCategory;
use App\Enums\AchievementLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Competition extends Model
{
    protected $fillable = [
        'title',
        'category',
        'level',
        'organizer',
        'registration_deadline',
        'registration_url',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'category' => AchievementCategory::class,
            'level' => AchievementLevel::class,
            'registration_deadline' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
