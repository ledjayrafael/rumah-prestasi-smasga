<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\StudentAccountCreator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    /** @var list<string> */
    public array $errors = [];

    /** @var list<array{name: string, nis: string, password: string}> */
    public array $credentials = [];

    public int $successCount = 0;

    private const MAX_ROWS = 500;

    public function __construct(
        private User $teacher,
        private StudentAccountCreator $creator,
    ) {}

    public function collection(Collection $rows): void
    {
        if ($rows->count() > self::MAX_ROWS) {
            $this->errors[] = 'File melebihi batas '.self::MAX_ROWS.' baris data.';

            return;
        }

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            $name = trim((string) ($row['nama'] ?? ''));
            $nis = trim((string) ($row['nis'] ?? ''));
            $className = trim((string) ($row['kelas'] ?? ''));

            if ($name === '' && $nis === '' && $className === '') {
                continue;
            }

            if ($name === '' || $nis === '' || $className === '') {
                $this->errors[] = "Baris {$line}: kolom nama, nis, dan kelas wajib diisi.";

                continue;
            }

            if (StudentProfile::where('nis', $nis)->exists()) {
                $this->errors[] = "Baris {$line}: NIS {$nis} sudah terdaftar.";

                continue;
            }

            $schoolClass = SchoolClass::query()
                ->where('name', $className)
                ->where('homeroom_teacher_id', $this->teacher->id)
                ->first();

            if ($schoolClass === null) {
                $this->errors[] = "Baris {$line}: kelas \"{$className}\" tidak ditemukan atau bukan kelas binaan Anda.";

                continue;
            }

            try {
                $result = $this->creator->create($name, $nis, $schoolClass->id);
                $this->successCount++;
                $this->credentials[] = [
                    'name' => $result['student']->name,
                    'nis' => $result['login'],
                    'password' => $result['password'],
                ];
            } catch (\Throwable) {
                $this->errors[] = "Baris {$line}: gagal membuat akun untuk NIS {$nis}.";
            }
        }
    }
}
