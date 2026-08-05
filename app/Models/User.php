<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\TeacherPosition;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'avatar_path',
        'role',
        'password',
        'must_change_password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    /** Kelas yang dibina (kelas binaan) untuk guru. */
    public function taughtClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_teacher');
    }

    /** Kelas where user is the official homeroom teacher (wali kelas). */
    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    /** Prestasi milik siswa ini. */
    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'student_id');
    }

    public function isSiswa(): bool
    {
        return $this->role === UserRole::Siswa;
    }

    public function isGuru(): bool
    {
        return $this->role === UserRole::Guru;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isDeveloper(): bool
    {
        return $this->role === UserRole::Developer;
    }

    public function isWaliKelas(): bool
    {
        if ($this->role !== UserRole::Guru) {
            return false;
        }

        return $this->teacherProfile?->position === TeacherPosition::WaliKelas;
    }

    /** @return Collection<int, int> */
    public function manageableClassIds(): Collection
    {
        return $this->homeroomClasses()->pluck('id');
    }

    public function canManageStudent(User $student): bool
    {
        if ($student->role !== UserRole::Siswa) {
            return false;
        }

        $classId = $student->studentProfile?->school_class_id;

        return $classId !== null && $this->manageableClassIds()->contains($classId);
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path
            ? route('siswa.avatar.show', $this)
            : null;
    }
}
