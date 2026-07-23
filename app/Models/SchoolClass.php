<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $fillable = [
        'name',
        'grade_level',
        'major',
        'homeroom_teacher_id',
    ];

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(StudentProfile::class);
    }

    /** Guru yang membina kelas ini (wali kelas / guru mapel). */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_teacher');
    }
}
