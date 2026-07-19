<?php

namespace App\Services;

use App\Exceptions\ImportTerlaluBanyakBarisException;
use App\Imports\PrestasiSiswaImport;
use App\Models\PrestasiSiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class PrestasiImportService
{
    private const REQUIRED_FIELDS = [
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
    ];

    private const BIDANG_MAPPING = [
        'akademik' => 'Akademik',
        'non-akademik' => 'Non Akademik',
        'keagamaan' => 'Keagamaan',
        'gtk' => 'GTK',
        'lembaga' => 'Lembaga',
    ];

    private const VALID_TINGKAT = [
        'kabupatenkota' => 'Kabupaten/Kota',
        'provinsi' => 'Provinsi',
        'nasional' => 'Nasional',
        'internasional' => 'Internasional',
    ];

    private const VALID_KATEGORI_KEGIATAN = [
        'individu' => 'Individu',
        'beregu' => 'Beregu',
    ];

    private const VALID_METODE_PELAKSANAAN = [
        'luring' => 'Luring',
        'daring' => 'Daring',
    ];

    /*
    |--------------------------------------------------------------------------
    | BATAS MAKSIMAL BARIS PER IMPORT
    |--------------------------------------------------------------------------
    | Diteruskan ke PrestasiSiswaImport, yang akan menghentikan pembacaan
    | file SAAT ITU JUGA begitu batas ini terlampaui -- bukan menunggu
    | seluruh file selesai dibaca dulu baru ketahuan kelebihan di akhir.
    |--------------------------------------------------------------------------
    */
    private const MAKS_BARIS_IMPORT = 7000;

    /*
    |--------------------------------------------------------------------------
    | DISK & PATH UNTUK TEMPORARY STORAGE (PENGGANTI SESSION)
    |--------------------------------------------------------------------------
    | Data hasil validasi import (bisa ribuan baris) TIDAK disimpan di session
    | lagi -- cukup token (string pendek) yang disimpan di session, sedangkan
    | isi datanya ditulis ke file JSON di storage/app/imports/prestasi.
    | Ini menghindari session membengkak (baik untuk driver file, database,
    | maupun cache/redis).
    */
    private const TEMP_DISK = 'local';
    private const TEMP_DIRECTORY = 'imports/prestasi';

    /*
    |--------------------------------------------------------------------------
    | VALIDASI FILE — logic BISNIS-nya persis sama seperti sebelumnya,
    | cuma dipindah dari controller ke sini (poin 7 permintaan Anda).
    |--------------------------------------------------------------------------
    */
    public function validateFile($file, int $madrasahId, string $submitter, int $periode): array
    {
        $normalizedMapping = collect(self::BIDANG_MAPPING)
            ->mapWithKeys(fn ($value) => [$this->normalizeKey($value) => $value])
            ->toArray();

        $errors = [];
        $result = [];

        $import = new PrestasiSiswaImport(self::MAKS_BARIS_IMPORT);

        // Titik utama optimasi memory: PrestasiSiswaImport sekarang
        // WithChunkReading, jadi Excel::import() ini membaca file secara
        // bertahap (per 500 baris), bukan memuat seluruh spreadsheet ke
        // memory sekaligus.
        //
        // FATAL: Kalau baris yang terbaca melebihi MAKS_BARIS_IMPORT,
        // PrestasiSiswaImport melempar ImportTerlaluBanyakBarisException
        // DI TENGAH proses baca (bukan menunggu file selesai dibaca dulu)
        // -- ditangkap di sini supaya file yang jelas kelewat besar
        // langsung dihentikan lebih awal, bukan diproses penuh dulu baru
        // ketahuan di akhir.
        try {
            Excel::import($import, $file);
        } catch (ImportTerlaluBanyakBarisException $e) {
            return [
                'result' => [],
                'errors' => [
                    [
                        'column' => 'general',
                        'message' => 'File melebihi batas maksimal ' . number_format($e->batasMaksimal, 0, ',', '.')
                            . ' baris. Pembacaan file dihentikan begitu batas ini terlampaui (tidak perlu menunggu '
                            . 'seluruh file selesai dibaca). Silakan pecah file menjadi beberapa bagian.',
                        'rows' => [],
                    ],
                ],
            ];
        }

        $data = $import->rows;

        foreach ($data as $index => $row) {

            // Rapikan seluruh kolom string
            foreach ($row as $key => $value) {
                if (is_string($value)) {
                    $row[$key] = preg_replace('/\s+/', ' ', trim($value));
                }
            }

            if (count($row) != 12) {
                $errors[] = [
                    'row' => $index + 2,
                    'error' => 'Jumlah kolom harus 12'
                ];
                continue;
            }

            // Validasi wajib isi
            foreach (self::REQUIRED_FIELDS as $field) {

                $value = $row[$field] ?? null;

                if (is_null($value) || trim((string) $value) === '') {

                    $errors[] = [
                        'row' => $index + 2,
                        'column' => $field,
                        'error' => 'Kolom wajib diisi'
                    ];

                    continue 2;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Bidang Prestasi (dibaca dari isi Excel, bukan dari halaman)
            |--------------------------------------------------------------------------
            */

            $bidang = $this->normalizeKey($row['bidang_prestasi']);

            if (!isset($normalizedMapping[$bidang])) {

                $errors[] = [
                    'row' => $index + 2,
                    'column' => 'bidang_prestasi',
                    'error' => 'Bidang prestasi tidak valid'
                ];

                continue;
            }

            $excelBidang = $normalizedMapping[$bidang];

            $row['bidang_prestasi'] = $excelBidang;

            /*
            |--------------------------------------------------------------------------
            | Validasi Tingkat
            |--------------------------------------------------------------------------
            */

            $tingkat = $this->normalizeKey($row['tingkat']);

            if (!isset(self::VALID_TINGKAT[$tingkat])) {

                $errors[] = [
                    'row' => $index + 2,
                    'column' => 'tingkat',
                    'error' => 'Tingkat harus Kabupaten/Kota, Provinsi, Nasional atau Internasional'
                ];

                continue;
            }

            $row['tingkat'] = self::VALID_TINGKAT[$tingkat];

            /*
            |--------------------------------------------------------------------------
            | Validasi Kategori Kegiatan
            |--------------------------------------------------------------------------
            */

            $kategori = $this->normalizeKey($row['kategori_kegiatan']);

            if (!isset(self::VALID_KATEGORI_KEGIATAN[$kategori])) {

                $errors[] = [
                    'row' => $index + 2,
                    'column' => 'kategori_kegiatan',
                    'error' => 'Kategori kegiatan harus Individu atau Beregu'
                ];

                continue;
            }

            $row['kategori_kegiatan'] = self::VALID_KATEGORI_KEGIATAN[$kategori];

            /*
            |--------------------------------------------------------------------------
            | Validasi Metode Pelaksanaan
            |--------------------------------------------------------------------------
            */

            $metode = $this->normalizeKey($row['metode_pelaksanaan']);

            if (!isset(self::VALID_METODE_PELAKSANAAN[$metode])) {

                $errors[] = [
                    'row' => $index + 2,
                    'column' => 'metode_pelaksanaan',
                    'error' => 'Metode pelaksanaan harus Luring atau Daring'
                ];

                continue;
            }

            $row['metode_pelaksanaan'] = self::VALID_METODE_PELAKSANAAN[$metode];

            /*
            |--------------------------------------------------------------------------
            | Validasi Skor
            |--------------------------------------------------------------------------
            */

            if (!is_numeric($row['skor'])) {

                $errors[] = [
                    'row' => $index + 2,
                    'column' => 'skor',
                    'error' => 'Skor harus berupa angka'
                ];

                continue;
            }

            $result[] = [
                'madrasah_id' => $madrasahId,
                'bidang_prestasi' => $excelBidang,
                'submitter' => $submitter,
                'nama_kegiatan' => $row['nama_kegiatan'],
                'tingkat' => $row['tingkat'],
                'kategori_kegiatan' => $row['kategori_kegiatan'],
                'juara' => $row['juara'],
                'lembaga_penyelenggara' => $row['lembaga_penyelenggara'],
                'kategori_penyelenggara' => $row['kategori_penyelenggara'],
                'waktu_kegiatan' => $row['waktu_kegiatan'],
                'metode_pelaksanaan' => $row['metode_pelaksanaan'],
                'skor' => $row['skor'],
                'link_drive_bukti' => $row['link_drive_bukti'],
                'keterangan' => $row['keterangan'] ?? null,
                'periode' => $periode,
            ];
        }

        $groupedErrors = collect($errors)
            ->groupBy(fn ($item) => ($item['column'] ?? 'general') . '|' . $item['error'])
            ->map(fn ($items) => [
                'column' => $items->first()['column'] ?? null,
                'message' => $items->first()['error'],
                'rows' => $items->pluck('row')->toArray()
            ])
            ->values()
            ->toArray();

        return [
            'result' => $result,
            'errors' => $groupedErrors,
        ];
    }

    private function normalizeKey($value): string
    {
        $value = strtolower(trim((string) $value));

        return preg_replace('/[^a-z0-9]/', '', $value);
    }

    /*
    |--------------------------------------------------------------------------
    | TEMP STORAGE (PENGGANTI SESSION)
    |--------------------------------------------------------------------------
    */

    /**
     * Simpan data hasil validasi ke file, kembalikan token pendek untuk
     * disimpan di session (bukan datanya langsung).
     */
    public function storeTemp(array $result): string
    {
        $token = (string) Str::uuid();

        Storage::disk(self::TEMP_DISK)->put(
            $this->tempPath($token),
            json_encode($result)
        );

        return $token;
    }

    /**
     * Baca kembali data berdasarkan token. Balikan array kosong kalau
     * token tidak valid/sudah kadaluarsa/file hilang -- supaya pemanggil
     * (preview/store_import) tetap bisa menangani "data kosong" seperti
     * alur lama, tanpa exception.
     */
    public function readTemp(?string $token): array
    {
        if (!$token) {
            return [];
        }

        $path = $this->tempPath($token);

        if (!Storage::disk(self::TEMP_DISK)->exists($path)) {
            return [];
        }

        $content = Storage::disk(self::TEMP_DISK)->get($path);

        return json_decode($content, true) ?? [];
    }

    public function deleteTemp(?string $token): void
    {
        if (!$token) {
            return;
        }

        Storage::disk(self::TEMP_DISK)->delete($this->tempPath($token));
    }

    private function tempPath(string $token): string
    {
        return self::TEMP_DIRECTORY . "/{$token}.json";
    }

    /*
    |--------------------------------------------------------------------------
    | BULK INSERT (PENGGANTI foreach + create() SATU-SATU)
    |--------------------------------------------------------------------------
    | Dari ribuan query INSERT terpisah menjadi beberapa query saja
    | (jumlah baris / $chunkSize). Karena pakai query builder insert()
    | (bukan Eloquent create()), event model/mutator TIDAK berjalan --
    | makanya waktu_kegiatan dikonversi manual ke format Y-m-d di sini
    | (sebelumnya otomatis lewat cast Eloquent 'date').
    */
    public function bulkInsert(array $rows, int $chunkSize = 500): int
    {
        $now = now();
        $totalInserted = 0;

        foreach (array_chunk($rows, $chunkSize) as $chunk) {

            $preparedChunk = array_map(function ($row) use ($now) {

                $row['waktu_kegiatan'] = !empty($row['waktu_kegiatan'])
                    ? Carbon::createFromFormat('d-m-Y', $row['waktu_kegiatan'])->format('Y-m-d')
                    : null;

                $row['created_at'] = $now;
                $row['updated_at'] = $now;

                return $row;
            }, $chunk);

            PrestasiSiswa::insert($preparedChunk);

            $totalInserted += count($preparedChunk);
        }

        return $totalInserted;
    }
}