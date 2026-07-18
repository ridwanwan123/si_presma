<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/*
|--------------------------------------------------------------------------
| SATU CLASS EXPORT UNTUK SEMUA KOMPONEN DASHBOARD
|--------------------------------------------------------------------------
| Daripada bikin class export terpisah untuk tiap card (Tren Sistem, Rata
| Jenjang, Kenaikan, Penurunan, Profil Madrasah), datanya sudah disiapkan
| sebagai array polos oleh DashboardController::export() -- class ini
| tinggal menerima data + heading + judul-nya saja lewat constructor.
|--------------------------------------------------------------------------
*/
class DashboardExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private Collection $data,
        private array $headings,
        private string $judul
    ) {
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        // Nama sheet Excel dibatasi maksimal 31 karakter
        return substr($this->judul, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}