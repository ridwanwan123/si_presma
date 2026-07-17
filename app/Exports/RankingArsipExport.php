<?php

namespace App\Exports;

use App\Models\RankingArsip;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RankingArsipExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private RankingArsip $arsip
    ) {
    }

    public function collection()
    {
        return $this->arsip->details()->orderBy('peringkat')->get();
    }

    public function headings(): array
    {
        return [
            'Peringkat',
            'Nama Madrasah',
            'NPSN',
            'Jenjang',
            'Kota',
            'Nilai Akademik',
            'Nilai Non Akademik',
            'Nilai Keagamaan',
            'Nilai GTK',
            'Nilai Lembaga',
            'Total Nilai Asesor (Sebelum Potongan)',
            'Potongan Aduan Masyarakat',
            'Potongan Keterlambatan',
            'Total Nilai Akhir',
            'Jumlah Prestasi Dinilai',
        ];
    }

    public function map($detail): array
    {
        return [
            $detail->peringkat,
            $detail->nama_madrasah,
            $detail->npsn,
            $detail->jenjang_madrasah,
            $detail->kota,
            $detail->nilai_akademik,
            $detail->nilai_non_akademik,
            $detail->nilai_keagamaan,
            $detail->nilai_gtk,
            $detail->nilai_lembaga,
            $detail->total_nilai_asesor,
            $detail->potongan_aduan,
            $detail->potongan_keterlambatan,
            $detail->total_nilai_akhir,
            $detail->jumlah_prestasi_dinilai,
        ];
    }

    public function title(): string
    {
        return 'Ranking ' . $this->arsip->periode;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}