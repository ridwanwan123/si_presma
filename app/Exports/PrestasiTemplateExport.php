<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PrestasiTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'bidang_prestasi',
            'nama_kegiatan',
            'tingkat',
            'kategori_kegiatan',
            'juara',
            'lembaga_penyelenggara',
            'kategori_penyelenggara',
            'waktu_kegiatan',
            'metode_pelaksanaan',
            'skor',
            'link_drive_bukti',
            'keterangan',
            'periode',
        ];
    }

    public function array(): array
    {
        return [
            // kosong
        ];
    }
}