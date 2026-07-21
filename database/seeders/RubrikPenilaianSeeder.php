<?php

namespace Database\Seeders;

use App\Models\RubrikPenilaian;
use Illuminate\Database\Seeder;

class RubrikPenilaianSeeder extends Seeder
{
    private const TAHUN_BERLAKU = 2026;

    /*
    |--------------------------------------------------------------------------
    | SEMUA 5 BIDANG SEKARANG TERISI:
    | - Akademik & Non Akademik  -> jenis_rubrik 'Lomba' (Tingkat x Juara)
    | - GTK                      -> jenis_rubrik 'Lomba' (kompetisi) DAN
    |                                'Karya' (karya tulis/publikasi)
    | - Keagamaan                -> jenis_rubrik 'Hafalan' (linear per Juz)
    | - Lembaga                  -> jenis_rubrik 'Kelembagaan' (paling
    |                                heterogen, lihat catatan di
    |                                seedLembaga() soal beberapa item yang
    |                                rentang angkanya perlu dikonfirmasi
    |                                ulang ke Anda)
    |--------------------------------------------------------------------------
    */
    public function run(): void
    {
        $this->seedPrestasiSiswa();
        $this->seedPrestasiGtkLomba();
        $this->seedGtkKarya();
        $this->seedHafalanQuran();
        $this->seedLembaga();
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
    | GTK -- JENIS KARYA TULIS/PUBLIKASI (bidang sama dengan GTK Lomba di
    | atas, cuma jenis_rubrik-nya 'Karya' -- kriterianya nama tetap, bukan
    | kombinasi Tingkat x Juara, jadi pakai kriteria_khusus).
    |
    | CATATAN: dokumen Juknis cuma kasih tabel ini sampai tingkat Provinsi
    | -- tidak ada baris Kabupaten/Kota untuk kategori Karya Tulis GTK.
    | Kalau ternyata memang ada, tambahkan manual lewat halaman Kelola
    | Rubrik nanti.
    |--------------------------------------------------------------------------
    */
    private function seedGtkKarya(): void
    {
        $data = [
            'Internasional' => [
                'Penulis Jurnal ISSN Terindeks Scopus' => 650,
                'Penulis Buku Tunggal ISBN' => 550,
                'Penulis Jurnal ISSN Belum Bereputasi Tinggi' => 450,
                'Karya Tulis Ilmiah' => 400,
                'Antologi Ber-ISBN' => 200,
            ],
            'Nasional' => [
                'Jurnal Nasional Terakreditasi Sinta 1-2 (Diakui Kemendikbudristek)' => 500,
                'Penulis Buku Tunggal ISBN' => 450,
                'Jurnal Nasional Terakreditasi Sinta 3-6 (Diakui Kemendikbudristek)' => 300,
                'Karya Tulis Ilmiah' => 250,
                'Antologi Ber-ISBN' => 75,
            ],
            'Provinsi' => [
                'Prosiding Terpublikasi (Belum Sekuat Jurnal)' => 250,
                'Artikel Populer Edukatif / Artikel Ilmiah Populer (Media Massa, Blog Ilmiah, Buletin Edukatif)' => 200,
                'Karya Tulis Ilmiah' => 150,
                'Antologi Ber-ISBN' => 50,
            ],
        ];

        foreach ($data as $tingkat => $daftarKriteria) {
            foreach ($daftarKriteria as $kriteria => $skor) {
                RubrikPenilaian::create([
                    'bidang_prestasi' => 'GTK',
                    'jenis_rubrik' => 'Karya',
                    'tingkat' => $tingkat,
                    'kriteria_khusus' => $kriteria,
                    'skor' => $skor,
                    'tahun_berlaku' => self::TAHUN_BERLAKU,
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LEMBAGA -- paling heterogen di seluruh Juknis. Sebagian besar item
    | punya skor TETAP (tinggal kriteria_khusus + skor), tapi beberapa
    | berbasis RENTANG ANGKA (nilai_min/nilai_max) -- persentase serapan,
    | jumlah siswa PASKIBRAKA, dst.
    |
    | !! PENTING -- MOHON DICEK ULANG !!
    | Beberapa rentang di bawah ini saya isi berdasarkan pembacaan dokumen
    | yang menurut saya BATASNYA SEDIKIT AMBIGU (kemungkinan ada bagian
    | yang kepotong/kurang jelas pas dokumennya di-scan):
    | 1. PASKIBRAKA: tertulis "9 > Lebih banyak" (saya baca sebagai >=9)
    |    DAN "7 - 9" sebagai tingkatan terpisah -- keduanya sama-sama
    |    menyentuh angka 9, jadi kalau persis 9 orang, bisa masuk 2
    |    kategori sekaligus. Saya set tingkatan pertama jadi >9 (bukan >=9)
    |    supaya tidak tumpang tindih, TAPI ini asumsi saya, bukan angka
    |    pasti dari dokumen.
    | 2. Sekolah Tinggi Kedinasan & MAN IC: tertulis "Diterima sampai 20%"
    |    di tengah antara ">30%" dan "kurang 10%" -- saya asumsikan
    |    maksudnya rentang 10%-30%, tapi dokumennya sendiri tidak
    |    menyebutkan batas bawah PERSIS jadi ini juga tebakan saya yang
    |    paling masuk akal, bukan kepastian.
    |--------------------------------------------------------------------------
    */
    private function seedLembaga(): void
    {
        // ---------- Item dengan SKOR TETAP (nama kriteria x tingkat) ----------
        $itemTetap = [
            'Nasional' => [
                'Zona Integritas/WBK - Lolos Penilaian Pendahuluan (Pendis)' => 150,
                'Zona Integritas/WBK - Lolos Tim Penilai Internal (Itjen)' => 350,
                'Zona Integritas/WBK - Lolos Tim Penilai Nasional (Predikat WBK)' => 500,
                'Adiwiyata' => 500,
                'Sekolah Sehat' => 400,
                'Ramah Anak' => 350,
            ],
            'Provinsi' => [
                'Adiwiyata' => 400,
                'Sekolah Sehat' => 350,
                'Ramah Anak' => 300,
            ],
            'Kabupaten/Kota' => [
                'Adiwiyata' => 150,
                'Sekolah Sehat' => 150,
                'Ramah Anak' => 150,
                'Madrasah Digital - Pelayanan' => 50,
                'Madrasah Digital - Pembelajaran' => 50,
                'Madrasah Digital - Asesmen' => 50,
                'IKPA Provinsi (KPPN)' => 150,
                'Donasi Tertinggi PMI/ZIS - Terbaik 1' => 100,
                'Donasi Tertinggi PMI/ZIS - Terbaik 2' => 75,
                'Donasi Tertinggi PMI/ZIS - Terbaik 3' => 50,
            ],
        ];

        foreach ($itemTetap as $tingkat => $daftarKriteria) {
            foreach ($daftarKriteria as $kriteria => $skor) {
                RubrikPenilaian::create([
                    'bidang_prestasi' => 'Lembaga',
                    'jenis_rubrik' => 'Kelembagaan',
                    'tingkat' => $tingkat,
                    'kriteria_khusus' => $kriteria,
                    'skor' => $skor,
                    'tahun_berlaku' => self::TAHUN_BERLAKU,
                ]);
            }
        }

        // ---------- Item berbasis RENTANG ANGKA ----------

        // Siswa MA masuk PASKIBRAKA Tingkat Provinsi -- rentang JUMLAH SISWA
        $paskibraka = [
            ['Lebih dari 9 orang', 9, 999, 250],
            ['7 - 8 orang', 7, 8, 200],
            ['4 - 6 orang', 4, 6, 150],
            ['1 - 3 orang', 1, 3, 100],
        ];
        foreach ($paskibraka as [$label, $min, $max, $skor]) {
            RubrikPenilaian::create([
                'bidang_prestasi' => 'Lembaga',
                'jenis_rubrik' => 'Kelembagaan',
                'tingkat' => 'Provinsi',
                'kriteria_khusus' => 'Siswa MA masuk PASKIBRAKA (' . $label . ')',
                'nilai_min' => $min,
                'nilai_max' => $max,
                'skor' => $skor,
                'tahun_berlaku' => self::TAHUN_BERLAKU,
            ]);
        }

        // Lulusan MA diterima di PTN/PTKI/PT LN -- rentang PERSENTASE SERAPAN
        $serapanPtn = [
            ['Serapan >= 90%', 90, 100, 350],
            ['Serapan 80% - 89,99%', 80, 89.99, 300],
            ['Serapan 70% - 79,99%', 70, 79.99, 250],
            ['Serapan 60% - 69,99%', 60, 69.99, 200],
            ['Serapan 50% - 59,99%', 50, 59.99, 150],
            ['Serapan 25% - 49,99%', 25, 49.99, 100],
        ];
        foreach ($serapanPtn as [$label, $min, $max, $skor]) {
            RubrikPenilaian::create([
                'bidang_prestasi' => 'Lembaga',
                'jenis_rubrik' => 'Kelembagaan',
                'tingkat' => 'Kabupaten/Kota',
                'kriteria_khusus' => 'Lulusan MA diterima PTN/PTKI/PT LN (' . $label . ')',
                'nilai_min' => $min,
                'nilai_max' => $max,
                'skor' => $skor,
                'tahun_berlaku' => self::TAHUN_BERLAKU,
            ]);
        }

        // Lulusan MA diterima di Sekolah Tinggi Kedinasan -- rentang % (LIHAT CATATAN AMBIGUITAS DI ATAS)
        $sekolahDinas = [
            ['Diterima > 30%', 30.01, 100, 200],
            ['Diterima 10% - 30%', 10, 30, 150],
            ['Diterima < 10%', 0, 9.99, 100],
        ];
        foreach ($sekolahDinas as [$label, $min, $max, $skor]) {
            RubrikPenilaian::create([
                'bidang_prestasi' => 'Lembaga',
                'jenis_rubrik' => 'Kelembagaan',
                'tingkat' => 'Kabupaten/Kota',
                'kriteria_khusus' => 'Lulusan MA diterima Sekolah Tinggi Kedinasan (' . $label . ')',
                'nilai_min' => $min,
                'nilai_max' => $max,
                'skor' => $skor,
                'tahun_berlaku' => self::TAHUN_BERLAKU,
            ]);
        }

        // Lulusan MTs diterima di MAN IC -- rentang % (LIHAT CATATAN AMBIGUITAS DI ATAS)
        $manIc = [
            ['Diterima > 30%', 30.01, 100, 200],
            ['Diterima 10% - 30%', 10, 30, 150],
            ['Diterima < 10%', 0, 9.99, 100],
        ];
        foreach ($manIc as [$label, $min, $max, $skor]) {
            RubrikPenilaian::create([
                'bidang_prestasi' => 'Lembaga',
                'jenis_rubrik' => 'Kelembagaan',
                'tingkat' => 'Kabupaten/Kota',
                'kriteria_khusus' => 'Lulusan MTs diterima MAN IC (' . $label . ')',
                'nilai_min' => $min,
                'nilai_max' => $max,
                'skor' => $skor,
                'tahun_berlaku' => self::TAHUN_BERLAKU,
            ]);
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