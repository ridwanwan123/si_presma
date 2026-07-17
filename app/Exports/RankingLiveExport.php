<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RankingLiveExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private \Illuminate\Support\Collection $ranking,
        private int $periode,
        private ?string $jenjangFilter
    ) {
    }

    public function collection()
    {
        return $this->ranking;
    }

    public function headings(): array
    {
        return [
            'Peringkat',
            'Nama Madrasah',
            'NPSN',
            'Jenjang',
            'Kota',
            'Total Sebelum Potongan',
            'Potongan Aduan Masyarakat',
            'Potongan Keterlambatan',
            'Total Nilai Akhir',
            'Jumlah Prestasi Dinilai',
        ];
    }

    public function map($item): array
    {
        return [
            $item->peringkat,
            $item->nama_madrasah,
            $item->npsn,
            $item->jenjang_madrasah,
            $item->kota,
            $item->total_sebelum_potongan,
            $item->potongan_aduan,
            $item->potongan_keterlambatan,
            $item->total_nilai,
            $item->jumlah_dinilai,
        ];
    }

    public function title(): string
    {
        $judul = 'Ranking ' . $this->periode;

        if ($this->jenjangFilter) {
            // Nama sheet Excel tidak boleh mengandung karakter "/" dst.
            $judul .= ' - ' . str_replace(['/', '\\', '*', '?', ':', '[', ']'], '-', $this->jenjangFilter);
        }

        return substr($judul, 0, 31); // batas nama sheet Excel adalah 31 karakter
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}