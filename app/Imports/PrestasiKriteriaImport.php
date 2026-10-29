<?php

namespace App\Imports;

use App\Exceptions\ImportTerlaluBanyakBarisException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

/*
|--------------------------------------------------------------------------
| IMPORT TEMPLATE B ("KRITERIA") -- dipakai GTK-Karya, Keagamaan-Hafalan,
| dan Lembaga. STRUKTUR SENGAJA DIPISAH dari PrestasiSiswaImport (Template
| A / "Lomba"), bukan digabung jadi satu class dengan percabangan --
| kolom yang dibaca memang beda: tidak ada juara/kategori_kegiatan/
| metode_pelaksanaan/kategori_penyelenggara sama sekali, sebagai gantinya
| ada kriteria_khusus (hasil pilihan dropdown, dicocokkan EXACT STRING ke
| RubrikPenilaian -- lihat RubrikPenilaian::cariKriteriaDariKoleksi()).
|
| method cleanText()/parseTanggal()/chunkSize() SENGAJA diduplikasi persis
| sama seperti PrestasiSiswaImport (bukan ditarik ke trait/parent class
| bersama) -- konsisten dengan prinsip yang sudah dipakai: dua file kecil
| yang jelas lebih gampang dirawat daripada satu abstraksi bersama yang
| isinya percabangan per template.
|--------------------------------------------------------------------------
*/
class PrestasiKriteriaImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public array $rows = [];

    public function __construct(
        private int $maksBaris = 7000
    ) {
    }

    private function cleanText($value)
    {
        if ($value instanceof RichText) {
            $value = $value->getPlainText();
        }

        $value = (string) $value;
        $value = strip_tags($value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim($value);
    }

    public function collection(Collection $rows)
    {
        // Sama seperti PrestasiSiswaImport: dipanggil BERULANG per chunk
        // (WithChunkReading), bukan sekali untuk seluruh file.
        foreach ($rows as $row) {
            $this->rows[] = [
                'bidang_prestasi'  => $this->cleanText($row['bidang_prestasi'] ?? null),
                'nama_kegiatan'    => $this->cleanText($row['nama_kegiatan'] ?? null),

                /*
                |----------------------------------------------------------------
                | 'tingkat' SENGAJA NULLABLE DI SINI -- kolom ini TIDAK ADA
                | sama sekali di template Keagamaan-Hafalan (lihat
                | PrestasiKriteriaTemplateExport::headings(), Hafalan tidak
                | punya dimensi tingkat, cuma dibedakan per "1 Juz".."30
                | Juz"). Karena pakai WithHeadingRow, $row['tingkat'] MEMANG
                | tidak akan terdefinisi kalau file yang diupload adalah
                | template Hafalan -- bukan bug, isset() di bawah ini yang
                | menjaga supaya tidak ada notice/error, bukan menganggap
                | user lupa isi kolom yang memang tidak ada.
                |----------------------------------------------------------------
                */
                'tingkat'          => isset($row['tingkat']) ? $this->cleanText($row['tingkat']) : null,

                'kriteria_khusus'  => $this->cleanText($row['kriteria_khusus'] ?? null),
                'waktu_kegiatan'   => $this->parseTanggal($row['waktu_kegiatan'] ?? null),
                'link_drive_bukti' => $row['link_drive_bukti'] ?? null,
                'keterangan'       => $this->cleanText($row['keterangan'] ?? null),
            ];

            if (count($this->rows) > $this->maksBaris) {
                throw new ImportTerlaluBanyakBarisException($this->maksBaris);
            }
        }
    }

    // Identik dengan PrestasiSiswaImport::parseTanggal() -- lihat catatan
    // di sana soal kenapa exception ditangkap di sini, bukan dibiarkan
    // menjatuhkan seluruh proses import dengan error 500 generik.
    private function parseTanggal($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Date::excelToDateTimeObject($value)->format('d-m-Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}