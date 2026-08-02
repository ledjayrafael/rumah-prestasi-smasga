<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsImportCredentialsExport implements FromCollection, WithHeadings
{
    /**
     * @param  list<array{name: string, nis: string, password: string}>  $credentials
     */
    public function __construct(private array $credentials) {}

    public function collection(): Collection
    {
        return collect($this->credentials)->map(fn (array $row) => [
            $row['name'],
            $row['nis'],
            $row['password'],
        ]);
    }

    public function headings(): array
    {
        return ['nama', 'nis', 'password_sementara'];
    }
}
