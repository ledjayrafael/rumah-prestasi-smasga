<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentAccountCreator
{
    /**
     * @return array{student: User, login: string, password: string}
     */
    public function create(string $name, string $nis, int $schoolClassId): array
    {
        $temporaryPassword = Str::password(10, symbols: false);

        $student = DB::transaction(function () use ($name, $nis, $schoolClassId, $temporaryPassword) {
            $student = User::create([
                'name' => $name,
                'username' => $nis,
                'role' => UserRole::Siswa,
                'password' => $temporaryPassword,
                'must_change_password' => true,
            ]);

            $student->studentProfile()->create([
                'nis' => $nis,
                'school_class_id' => $schoolClassId,
            ]);

            return $student;
        });

        return [
            'student' => $student,
            'login' => $nis,
            'password' => $temporaryPassword,
        ];
    }
}
