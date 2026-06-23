<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PrestasiSiswaImport implements ToCollection, WithHeadingRow
{
    public array $rows = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->rows[] = [
                'bidang_prestasi' => $row['bidang_prestasi'] ?? null,
                'nama_kegiatan' => $row['nama_kegiatan'] ?? null,
                'tingkat' => $row['tingkat'] ?? null,
                'kategori_kegiatan' => $row['kategori_kegiatan'] ?? null,
                'juara' => $row['juara'] ?? null,
                'lembaga_penyelenggara' => $row['lembaga_penyelenggara'] ?? null,
                'kategori_penyelenggara' => $row['kategori_penyelenggara'] ?? null,
                'waktu_kegiatan' =>
    !empty($row['waktu_kegiatan'])
        ? Date::excelToDateTimeObject($row['waktu_kegiatan'])
            ->format('d-m-Y')
        : null,
                'skor_luring' => $row['skor_luring'] ?? 0,
                'skor_daring' => $row['skor_daring'] ?? 0,
                'link_drive_bukti' => $row['link_drive_bukti'] ?? null,
                'keterangan' => $row['keterangan'] ?? null,
                'periode' => $row['periode'] ?? null,
            ];
        }
    }
}