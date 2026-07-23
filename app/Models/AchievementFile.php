<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AchievementFile extends Model
{
    protected $fillable = [
        'achievement_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
