<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RankingLiveSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(
        private Collection $data,
        private string $judulSheet,
        private bool $isTotal
    ) {
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        if ($this->isTotal) {
            return [
                'Peringkat', 'Nama Madrasah', 'NPSN', 'Jenjang', 'Kota',
                'Total Nilai Mentah', 'Total Potongan', 'Total Nilai Akhir', 'Jumlah Dinilai',
            ];
        }

        return [
            'Peringkat', 'Nama Madrasah', 'NPSN', 'Jenjang', 'Kota',
            'Nilai Mentah', 'Potongan Aduan', 'Potongan Keterlambatan', 'Total Potongan', 'Nilai Akhir',
        ];
    }

    public function map($item): array
    {
        if ($this->isTotal) {
            return [
                $item->peringkat,
                $item->nama_madrasah,
                $item->npsn,
                $item->jenjang_madrasah,
                $item->kota,
                $item->total_nilai_mentah,
                $item->total_potongan,
                $item->total_nilai_akhir,
                $item->jumlah_dinilai,
            ];
        }

        return [
            $item->peringkat,
            $item->nama_madrasah,
            $item->npsn,
            $item->jenjang_madrasah,
            $item->kota,
            $item->nilai_mentah,
            $item->potongan_aduan,
            $item->potongan_keterlambatan,
            $item->total_potongan,
            $item->nilai_akhir,
        ];
    }

    public function title(): string
    {
        // Nama sheet Excel dibatasi maksimal 31 karakter
        return substr($this->judulSheet, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}