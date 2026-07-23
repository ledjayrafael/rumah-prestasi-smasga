<?php

namespace App\Models;

use App\Enums\TeacherPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'position' => TeacherPosition::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
