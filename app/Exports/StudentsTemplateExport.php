<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsTemplateExport implements FromCollection, WithHeadings
{
    public function __construct(private User $teacher) {}

    public function collection(): Collection
    {
        $exampleClass = $this->teacher
            ->homeroomClasses()
            ->orderBy('name')
            ->value('name');

        if ($exampleClass === null) {
            return collect([
                ['Contoh Siswa', '12345', 'NAMA_KELAS_ANDA'],
            ]);
        }

        return collect([
            ['Contoh Siswa', '12345', $exampleClass],
        ]);
    }

    public function headings(): array
    {
        return ['nama', 'nis', 'kelas'];
    }
}
