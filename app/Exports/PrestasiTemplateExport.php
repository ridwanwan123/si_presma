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
            // 'periode' SENGAJA DIHAPUS -- kolom ini tidak pernah dibaca oleh
            // PrestasiSiswaImport (lihat collection(), yang cuma memetakan 12
            // kolom di atas), dan nilai periode SELALU diambil dari
            // PeriodeAktif::aktif() di PrestasiController, bukan dari isi
            // file Excel. Mempertahankan kolom ini di template cuma bikin
            // user salah kira bisa menentukan periode sendiri lewat Excel.
        ];
    }

    public function array(): array
    {
        return [
            // kosong
        ];
    }
}