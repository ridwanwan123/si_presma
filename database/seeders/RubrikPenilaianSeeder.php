<?php

namespace Database\Seeders;

use App\Models\RubrikPenilaian;
use Illuminate\Database\Seeder;

class RubrikPenilaianSeeder extends Seeder
{
    private const TAHUN_BERLAKU = 2026;

    /*
    |--------------------------------------------------------------------------
    | CATATAN: Seeder ini BARU mengisi kategori yang strukturnya jelas
    | (kombinasi Tingkat x Juara): Prestasi Siswa (Akademik/Non Akademik) dan
    | Prestasi GTK jenis Lomba, plus Hafalan Qur'an (linear per Juz).
    |
    | BELUM diisi: GTK jenis Karya Tulis (Penulis Jurnal, dst) dan Lembaga
    | (Adiwiyata, Zona Integritas, dst) -- keduanya heterogen banget, tiap
    | baris kriterianya beda nama & logic sendiri, jadi didata manual satu
    | per satu nanti kalau sudah disepakati cara input-nya di sisi Madrasah.
    |--------------------------------------------------------------------------
    */
    public function run(): void
    {
        $this->seedPrestasiSiswa();
        $this->seedPrestasiGtkLomba();
        $this->seedHafalanQuran();
    }

    /*
    |--------------------------------------------------------------------------
    | PRESTASI SISWA (Akademik / Non Akademik) -- Tingkat x Juara x
    | Individu/Beregu x Luring/Daring x Pemerintah/Non-Pemerintah
    |--------------------------------------------------------------------------
    */
    private function seedPrestasiSiswa(): void
    {
        // [individu_luring, individu_daring, beregu_luring, beregu_daring]
        $skorPemerintah = [
            'Internasional' => [
                'Juara 1' => [600, 400, 300, 200], 'Juara 2' => [575, 383, 288, 192], 'Juara 3' => [550, 366, 276, 184],
                'Harapan 1' => [525, 349, 264, 176], 'Harapan 2' => [500, 332, 252, 168],
            ],
            'Nasional' => [
                'Juara 1' => [450, 298, 228, 152], 'Juara 2' => [425, 281, 216, 144], 'Juara 3' => [400, 264, 204, 136],
                'Harapan 1' => [375, 247, 192, 128], 'Harapan 2' => [350, 230, 180, 120],
            ],
            'Provinsi' => [
                'Juara 1' => [300, 196, 156, 104], 'Juara 2' => [275, 179, 144, 96], 'Juara 3' => [250, 162, 132, 88],
                'Harapan 1' => [225, 145, 120, 80], 'Harapan 2' => [200, 128, 108, 72],
            ],
            'Kabupaten/Kota' => [
                'Juara 1' => [150, 94, 84, 56], 'Juara 2' => [125, 77, 72, 48], 'Juara 3' => [100, 60, 60, 40],
                'Harapan 1' => [75, 43, 48, 32], 'Harapan 2' => [50, 26, 36, 24],
            ],
        ];

        $skorNonPemerintah = [
            'Internasional' => [
                'Juara 1' => [200, 100, 100, 75], 'Juara 2' => [192, 95, 96, 72], 'Juara 3' => [184, 90, 92, 69],
                'Harapan 1' => [176, 85, 88, 66], 'Harapan 2' => [168, 80, 84, 63],
            ],
            'Nasional' => [
                'Juara 1' => [152, 70, 76, 57], 'Juara 2' => [144, 65, 72, 54], 'Juara 3' => [136, 60, 68, 51],
                'Harapan 1' => [128, 55, 64, 48], 'Harapan 2' => [120, 50, 60, 45],
            ],
            'Provinsi' => [
                'Juara 1' => [104, 40, 52, 39], 'Juara 2' => [96, 35, 48, 36], 'Juara 3' => [88, 30, 44, 33],
                'Harapan 1' => [80, 25, 40, 30], 'Harapan 2' => [72, 20, 36, 27],
            ],
            'Kabupaten/Kota' => [
                'Juara 1' => [56, 10, 28, 21], 'Juara 2' => [48, 8, 24, 18], 'Juara 3' => [40, 6, 20, 15],
                'Harapan 1' => [32, 4, 16, 12], 'Harapan 2' => [24, 3, 12, 9],
            ],
        ];

        foreach (['Pemerintah' => $skorPemerintah, 'Non Pemerintah' => $skorNonPemerintah] as $penyelenggara => $tabelTingkat) {
            foreach ($tabelTingkat as $tingkat => $tabelJuara) {
                foreach ($tabelJuara as $juara => $skor) {
                    [$individuLuring, $individuDaring, $beregeuLuring, $beregeuDaring] = $skor;

                    $baris = [
                        ['Individu', 'Luring', $individuLuring],
                        ['Individu', 'Daring', $individuDaring],
                        ['Beregu', 'Luring', $beregeuLuring],
                        ['Beregu', 'Daring', $beregeuDaring],
                    ];

                    foreach ($baris as [$kategoriKegiatan, $metode, $nilaiSkor]) {
                        // Berlaku untuk Akademik & Non Akademik -- Juknis tidak
                        // membedakan skornya per bidang, tabelnya sama untuk
                        // keduanya (cuma beda label bidang di prestasi_siswas).
                        foreach (['Akademik', 'Non Akademik'] as $bidang) {
                            RubrikPenilaian::create([
                                'bidang_prestasi' => $bidang,
                                'jenis_rubrik' => 'Lomba',
                                'tingkat' => $tingkat,
                                'juara' => $juara,
                                'kategori_kegiatan' => $kategoriKegiatan,
                                'metode_pelaksanaan' => $metode,
                                'kategori_penyelenggara' => $penyelenggara,
                                'skor' => $nilaiSkor,
                                'tahun_berlaku' => self::TAHUN_BERLAKU,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PRESTASI GTK -- JENIS LOMBA (tanpa dimensi Luring/Daring)
    |--------------------------------------------------------------------------
    */
    private function seedPrestasiGtkLomba(): void
    {
        // [individu, beregu]
        $skorPemerintah = [
            'Internasional' => [
                'Juara 1' => [600, 300], 'Juara 2' => [575, 288], 'Juara 3' => [550, 276],
                'Harapan 1' => [525, 264], 'Harapan 2' => [500, 252],
            ],
            'Nasional' => [
                'Juara 1' => [450, 228], 'Juara 2' => [425, 216], 'Juara 3' => [400, 204],
                'Harapan 1' => [375, 192], 'Harapan 2' => [350, 180],
            ],
            'Provinsi' => [
                'Juara 1' => [300, 156], 'Juara 2' => [275, 144], 'Juara 3' => [250, 132],
                'Harapan 1' => [225, 120], 'Harapan 2' => [200, 108],
            ],
            'Kabupaten/Kota' => [
                'Juara 1' => [150, 84], 'Juara 2' => [125, 72], 'Juara 3' => [100, 60],
                'Harapan 1' => [75, 43], 'Harapan 2' => [50, 26],
            ],
        ];

        $skorNonPemerintah = [
            'Internasional' => [
                'Juara 1' => [200, 100], 'Juara 2' => [192, 96], 'Juara 3' => [184, 92],
                'Harapan 1' => [176, 88], 'Harapan 2' => [168, 84],
            ],
            'Nasional' => [
                'Juara 1' => [152, 76], 'Juara 2' => [144, 72], 'Juara 3' => [136, 68],
                'Harapan 1' => [128, 64], 'Harapan 2' => [120, 60],
            ],
            'Provinsi' => [
                'Juara 1' => [104, 52], 'Juara 2' => [96, 48], 'Juara 3' => [88, 44],
                'Harapan 1' => [80, 40], 'Harapan 2' => [72, 36],
            ],
            'Kabupaten/Kota' => [
                'Juara 1' => [56, 28], 'Juara 2' => [48, 24], 'Juara 3' => [40, 20],
                'Harapan 1' => [32, 16], 'Harapan 2' => [24, 12],
            ],
        ];

        foreach (['Pemerintah' => $skorPemerintah, 'Non Pemerintah' => $skorNonPemerintah] as $penyelenggara => $tabelTingkat) {
            foreach ($tabelTingkat as $tingkat => $tabelJuara) {
                foreach ($tabelJuara as $juara => [$individu, $beregu]) {
                    foreach (['Individu' => $individu, 'Beregu' => $beregu] as $kategoriKegiatan => $nilaiSkor) {
                        RubrikPenilaian::create([
                            'bidang_prestasi' => 'GTK',
                            'jenis_rubrik' => 'Lomba',
                            'tingkat' => $tingkat,
                            'juara' => $juara,
                            'kategori_kegiatan' => $kategoriKegiatan,
                            'metode_pelaksanaan' => null, // GTK-lomba tidak punya dimensi ini
                            'kategori_penyelenggara' => $penyelenggara,
                            'skor' => $nilaiSkor,
                            'tahun_berlaku' => self::TAHUN_BERLAKU,
                        ]);
                    }
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HAFALAN QUR'AN -- linear, 25 poin per Juz (1-30)
    |--------------------------------------------------------------------------
    */
    private function seedHafalanQuran(): void
    {
        for ($juz = 1; $juz <= 30; $juz++) {
            RubrikPenilaian::create([
                'bidang_prestasi' => 'Keagamaan',
                'jenis_rubrik' => 'Hafalan',
                'kriteria_khusus' => $juz . ' Juz',
                'nilai_min' => $juz,
                'nilai_max' => $juz,
                'skor' => $juz * 25,
                'tahun_berlaku' => self::TAHUN_BERLAKU,
            ]);
        }
    }
}