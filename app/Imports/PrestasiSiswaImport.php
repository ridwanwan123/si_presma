<?php

namespace App\Imports;

use App\Exceptions\ImportTerlaluBanyakBarisException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class PrestasiSiswaImport implements ToCollection, WithHeadingRow, WithChunkReading
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
        // NOTE: dengan WithChunkReading, method ini dipanggil BERULANG KALI
        // per chunk (bukan sekali untuk seluruh file) -- Laravel Excel/
        // PhpSpreadsheet jadi membaca file sedikit demi sedikit dari disk,
        // bukan memuat seluruh spreadsheet ke memory sekaligus. Ini yang
        // paling menentukan penurunan pemakaian memory saat parsing file
        // besar (5.000-20.000 baris), independen dari optimasi lain.
        foreach ($rows as $row) {
            $this->rows[] = [
                'bidang_prestasi'           => $this->cleanText($row['bidang_prestasi'] ?? null),
                'nama_kegiatan'             => $this->cleanText($row['nama_kegiatan'] ?? null),
                'tingkat'                   => $this->cleanText($row['tingkat'] ?? null),
                'kategori_kegiatan'         => $this->cleanText($row['kategori_kegiatan'] ?? null),
                'juara'                     => $this->cleanText($row['juara'] ?? null),
                'lembaga_penyelenggara'     => $this->cleanText($row['lembaga_penyelenggara'] ?? null),
                'kategori_penyelenggara'    => $this->cleanText($row['kategori_penyelenggara'] ?? null),
                'waktu_kegiatan'            => $this->parseTanggal($row['waktu_kegiatan'] ?? null),
                'metode_pelaksanaan'        => $this->cleanText($row['metode_pelaksanaan'] ?? null),
                'skor'                      => $row['skor'] ?? 0,
                'link_drive_bukti'          => $row['link_drive_bukti'] ?? null,
                'keterangan'                => $this->cleanText($row['keterangan'] ?? null),
            ];

            /*
            |--------------------------------------------------------------------------
            | HENTIKAN SEGERA BEGITU MELEBIHI BATAS
            |--------------------------------------------------------------------------
            | Dicek TIAP BARIS (bukan nunggu satu chunk penuh dulu), supaya
            | begitu baris ke (maksBaris+1) kedeteksi, pembacaan file
            | langsung berhenti SAAT ITU JUGA -- file 20.000 baris cuma
            | akan sempat kebaca ±maksBaris baris, bukan 20.000-nya
            | sekaligus, sebelum user tahu filenya kelebihan.
            |--------------------------------------------------------------------------
            */
            if (count($this->rows) > $this->maksBaris) {
                throw new ImportTerlaluBanyakBarisException($this->maksBaris);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PARSE TANGGAL — DIBUAT AMAN TERHADAP NILAI RUSAK
    |--------------------------------------------------------------------------
    | Date::excelToDateTimeObject() akan melempar exception kalau isi cell
    | bukan serial tanggal Excel yang valid (mis. user ketik teks bebas di
    | kolom tanggal). Sebelumnya exception ini TIDAK ditangkap sama sekali,
    | jadi satu cell tanggal rusak bisa bikin SELURUH proses import gagal
    | dengan error 500 generik -- bukan pesan per-baris yang jelas.
    |
    | Sekarang: kalau gagal parse, nilai mentahnya tetap disimpan apa adanya
    | (bukan exception), supaya PrestasiImportService::validateFile() yang
    | menentukan pesan errornya secara rapi per baris/kolom, konsisten
    | dengan validasi kolom lain.
    */
    private function parseTanggal($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Date::excelToDateTimeObject($value)->format('d-m-Y');
        } catch (\Throwable $e) {
            // Kembalikan sebagai teks mentah -- akan gagal di validasi
            // format tanggal pada validateFile(), bukan crash di sini.
            return (string) $value;
        }
    }

    /**
     * Ukuran chunk pembacaan file. 500 baris per chunk adalah titik
     * seimbang antara jumlah query/overhead per-chunk vs pemakaian memory
     * -- cukup kecil untuk menjaga memory tetap rendah, cukup besar supaya
     * tidak terlalu banyak siklus baca-parsing untuk file 20.000 baris.
     */
    public function chunkSize(): int
    {
        return 500;
    }
}