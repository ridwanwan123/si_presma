<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class PrestasiSiswaImport implements ToCollection, WithHeadingRow
{
    public array $rows = [];

    private function cleanText($value)
    {
        if ($value instanceof RichText) {
            $value = $value->getPlainText();
        }

        // Pastikan menjadi string biasa
        $value = (string) $value;

        // Hilangkan tag HTML jika ada
        $value = strip_tags($value);

        // Rapikan spasi
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim($value);
    }
    
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->rows[] = [
                'bidang_prestasi'           => $this->cleanText($row['bidang_prestasi'] ?? null),
                'nama_kegiatan'             => $this->cleanText($row['nama_kegiatan'] ?? null),
                'tingkat'                   => $this->cleanText($row['tingkat'] ?? null),
                'kategori_kegiatan'         => $this->cleanText($row['kategori_kegiatan'] ?? null),
                'juara'                     => $this->cleanText($row['juara'] ?? null),
                'lembaga_penyelenggara'     => $this->cleanText($row['lembaga_penyelenggara'] ?? null),
                'kategori_penyelenggara'    => $this->cleanText($row['kategori_penyelenggara'] ?? null),
                'waktu_kegiatan'            => !empty($row['waktu_kegiatan']) ? Date::excelToDateTimeObject($row['waktu_kegiatan'])->format('d-m-Y') : null,
                'skor_luring'               => $row['skor_luring'] ?? 0,
                'skor_daring'               => $row['skor_daring'] ?? 0,
                'link_drive_bukti'          => $row['link_drive_bukti'] ?? null,
                'keterangan'                => $this->cleanText($row['keterangan'] ?? null),
            ];
        }
    }
}