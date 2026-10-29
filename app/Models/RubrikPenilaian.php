<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RubrikPenilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'bidang_prestasi',
        'jenis_rubrik',
        'tingkat',
        'juara',
        'kategori_kegiatan',
        'metode_pelaksanaan',
        'kategori_penyelenggara',
        'kriteria_khusus',
        'nilai_min',
        'nilai_max',
        'skor',
        'keterangan',
        'tahun_berlaku',
    ];

    /*
    |--------------------------------------------------------------------------
    | CARI RUBRIK YANG COCOK -- untuk kategori "Lomba" (siswa & GTK-lomba),
    | yaitu yang kriterianya berbasis kolom terstruktur (tingkat, juara, dst),
    | BUKAN kriteria_khusus/rentang angka.
    |--------------------------------------------------------------------------
    | Balikan null kalau tidak ada rubrik yang cocok persis -- pemanggil
    | (nanti di AsesorController) tinggal cek null sebagai "tidak match".
    |--------------------------------------------------------------------------
    */
    public static function cariRubrikLomba(
        string $bidangPrestasi,
        string $tingkat,
        string $juara,
        string $kategoriKegiatan,
        ?string $metodePelaksanaan,
        ?string $kategoriPenyelenggara,
        int $tahun
    ): ?self {
        return static::query()
            ->where('bidang_prestasi', $bidangPrestasi)
            ->where('jenis_rubrik', 'Lomba')
            ->where('tingkat', $tingkat)
            ->where('juara', $juara)
            ->where('kategori_kegiatan', $kategoriKegiatan)
            ->where('tahun_berlaku', $tahun)
            // GTK tidak punya dimensi Luring/Daring (kolom metode_pelaksanaan
            // di rubrik-nya sengaja NULL) -- baris begitu harus TETAP cocok
            // apapun metode_pelaksanaan prestasi aslinya. Makanya dicek
            // "sama ATAU rubriknya memang null", bukan "harus sama persis".
            ->where(function ($q) use ($metodePelaksanaan) {
                $q->whereNull('metode_pelaksanaan')
                    ->orWhere('metode_pelaksanaan', $metodePelaksanaan);
            })
            ->when($kategoriPenyelenggara, fn ($q) => $q->where('kategori_penyelenggara', $kategoriPenyelenggara))
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS KECOCOKAN -- dipakai langsung oleh AsesorController::show().
    | Membungkus cariRubrikLomba() + perbandingan skor jadi satu pemanggilan,
    | balikannya sudah siap pakai buat badge di Blade.
    |--------------------------------------------------------------------------
    | 'tidak_ada' = kombinasi kriteria belum ada di tabel rubrik sama sekali
    | 'cocok'     = ada rubriknya, DAN skor Madrasah sama persis
    | 'tidak_cocok' = ada rubriknya, TAPI skor Madrasah berbeda
    |--------------------------------------------------------------------------
    */
    public static function statusKecocokan(
        string $bidangPrestasi,
        ?string $tingkat,
        ?string $juara,
        ?string $kategoriKegiatan,
        ?string $metodePelaksanaan,
        ?string $kategoriPenyelenggara,
        int $tahun,
        float $skorMadrasah
    ): array {
        // Kalau salah satu kriteria wajib kosong (data prestasi tidak lengkap),
        // jangan dipaksa query -- langsung anggap "tidak ada rubrik".
        if (!$tingkat || !$juara || !$kategoriKegiatan) {
            return ['status' => 'tidak_ada', 'skor_rubrik' => null];
        }

        $rubrik = static::cariRubrikLomba(
            $bidangPrestasi,
            $tingkat,
            $juara,
            $kategoriKegiatan,
            $metodePelaksanaan,
            $kategoriPenyelenggara,
            $tahun
        );

        if (!$rubrik) {
            return ['status' => 'tidak_ada', 'skor_rubrik' => null];
        }

        $cocok = abs((float) $rubrik->skor - $skorMadrasah) < 0.01;

        return [
            'status' => $cocok ? 'cocok' : 'tidak_cocok',
            'skor_rubrik' => (float) $rubrik->skor,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CARI RUBRIK "KRITERIA BEBAS" -- untuk jenis_rubrik 'Karya' (GTK) dan
    | 'Kelembagaan' (Lembaga). Kriterianya bukan Tingkat x Juara, tapi
    | Tingkat x satu label bebas (kriteria_khusus) yang dicocokkan PERSIS
    | sama teksnya (bukan LIKE, bukan hitung rentang angka) -- konsisten
    | dengan keputusan: madrasah pilih dari DROPDOWN, bukan ngetik angka
    | mentah, jadi "Serapan >= 90%" itu satu opsi utuh, bukan dihitung dari
    | input persen bebas.
    |
    | $jenisRubrik sengaja jadi PARAMETER (bukan di-hardcode 'Karya' atau
    | 'Kelembagaan') -- satu method ini dipakai buat KEDUANYA, karena
    | struktur pencocokannya identik, cuma beda bidang & jenis_rubrik.
    |--------------------------------------------------------------------------
    */
    public static function cariRubrikKriteria(
        string $bidangPrestasi,
        string $jenisRubrik,
        ?string $tingkat,
        string $kriteriaKhusus,
        int $tahun
    ): ?self {
        return static::query()
            ->where('bidang_prestasi', $bidangPrestasi)
            ->where('jenis_rubrik', $jenisRubrik)
            // '= NULL' TIDAK PERNAH match di SQL (harus IS NULL) -- makanya
            // dicabang manual, bukan ->where('tingkat', $tingkat) polos.
            // Kasus $tingkat null ini yang dipakai Hafalan (Keagamaan).
            ->when(
                $tingkat === null,
                fn ($q) => $q->whereNull('tingkat'),
                fn ($q) => $q->where('tingkat', $tingkat)
            )
            ->where('kriteria_khusus', $kriteriaKhusus)
            ->where('tahun_berlaku', $tahun)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | MUAT SEMUA RUBRIK "KRITERIA BEBAS" UNTUK 1 JENIS + 1 TAHUN -- SEKALI
    | QUERY. Sama alasannya dengan muatUntukPencocokan(): dipakai import
    | massal supaya tidak query per baris Excel.
    |--------------------------------------------------------------------------
    */
    public static function muatKriteriaUntukPencocokan(string $jenisRubrik, int $tahun): \Illuminate\Support\Collection
    {
        return static::where('jenis_rubrik', $jenisRubrik)
            ->where('tahun_berlaku', $tahun)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | VERSI IN-MEMORY dari cariRubrikKriteria() -- LOGIC HARUS SAMA PERSIS,
    | sama aturan seperti cariDariKoleksi() vs cariRubrikLomba().
    |--------------------------------------------------------------------------
    */
    public static function cariKriteriaDariKoleksi(
        \Illuminate\Support\Collection $daftarRubrik,
        string $bidangPrestasi,
        ?string $tingkat,
        string $kriteriaKhusus
    ): ?self {
        return $daftarRubrik->first(function ($r) use ($bidangPrestasi, $tingkat, $kriteriaKhusus) {
            return $r->bidang_prestasi === $bidangPrestasi
                // Perbandingan '===' PHP sudah benar untuk null vs null
                // (beda dari kasus SQL di cariRubrikKriteria() di atas) --
                // null === null itu true, jadi tidak perlu percabangan.
                && $r->tingkat === $tingkat
                && $r->kriteria_khusus === $kriteriaKhusus;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MUAT SEMUA RUBRIK "RENTANG" UNTUK 1 BIDANG+JENIS+TAHUN -- dipakai
    | import massal Hafalan Qur'an (Keagamaan), biar tidak query per baris
    | seperti cariRubrikRentang() kalau dipanggil langsung dalam loop.
    | TIDAK dipakai Lembaga -- Lembaga sengaja pakai kriteria_khusus teks
    | (dropdown), bukan hitung rentang, meski kolom nilai_min/nilai_max-nya
    | ikut terisi di seeder (disimpan buat dokumentasi/fallback, bukan
    | dipakai matching saat ini).
    |--------------------------------------------------------------------------
    */
    public static function muatRentangUntukPencocokan(string $bidangPrestasi, string $jenisRubrik, int $tahun): \Illuminate\Support\Collection
    {
        return static::where('bidang_prestasi', $bidangPrestasi)
            ->where('jenis_rubrik', $jenisRubrik)
            ->where('tahun_berlaku', $tahun)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | VERSI IN-MEMORY dari cariRubrikRentang() -- LOGIC HARUS SAMA PERSIS
    | (nilai_min <= nilai <= nilai_max).
    |--------------------------------------------------------------------------
    */
    public static function cariRentangDariKoleksi(\Illuminate\Support\Collection $daftarRubrik, float $nilai): ?self
    {
        return $daftarRubrik->first(function ($r) use ($nilai) {
            return (float) $r->nilai_min <= $nilai && (float) $r->nilai_max >= $nilai;
        });
    }

    public static function cariRubrikRentang(string $bidangPrestasi, string $jenisRubrik, float $nilai, int $tahun): ?self
    {
        return static::query()
            ->where('bidang_prestasi', $bidangPrestasi)
            ->where('jenis_rubrik', $jenisRubrik)
            ->where('tahun_berlaku', $tahun)
            ->where('nilai_min', '<=', $nilai)
            ->where('nilai_max', '>=', $nilai)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | MUAT SEMUA RUBRIK "LOMBA" UNTUK 1 TAHUN -- SEKALI QUERY.
    |--------------------------------------------------------------------------
    | Dipakai import massal (PrestasiImportService) supaya TIDAK query ke
    | rubrik_penilaians per baris Excel (bisa sampai 7.000 query kalau
    | naive) -- cukup 1 query di awal, sisanya dicocokkan di memori lewat
    | cariDariKoleksi().
    |--------------------------------------------------------------------------
    */
    public static function muatUntukPencocokan(int $tahun): \Illuminate\Support\Collection
    {
        return static::where('jenis_rubrik', 'Lomba')
            ->where('tahun_berlaku', $tahun)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | VERSI IN-MEMORY dari cariRubrikLomba() -- LOGIC PENCOCOKAN HARUS SAMA
    | PERSIS (termasuk aturan GTK: metode_pelaksanaan null = wildcard),
    | supaya hasil import massal konsisten dengan badge kecocokan yang
    | sudah dipakai di halaman Asesor. Kalau nanti aturan pencocokan
    | berubah, ubah DUA method ini bersamaan.
    |--------------------------------------------------------------------------
    */
    public static function cariDariKoleksi(
        \Illuminate\Support\Collection $daftarRubrik,
        string $bidangPrestasi,
        string $tingkat,
        string $juara,
        string $kategoriKegiatan,
        ?string $metodePelaksanaan,
        ?string $kategoriPenyelenggara
    ): ?self {
        return $daftarRubrik->first(function ($r) use (
            $bidangPrestasi, $tingkat, $juara, $kategoriKegiatan, $metodePelaksanaan, $kategoriPenyelenggara
        ) {
            return $r->bidang_prestasi === $bidangPrestasi
                && $r->tingkat === $tingkat
                && $r->juara === $juara
                && $r->kategori_kegiatan === $kategoriKegiatan
                // GTK: rubrik metode_pelaksanaan NULL = wildcard, sama
                // seperti cariRubrikLomba().
                && ($r->metode_pelaksanaan === null || $r->metode_pelaksanaan === $metodePelaksanaan)
                && (!$kategoriPenyelenggara || $r->kategori_penyelenggara === $kategoriPenyelenggara);
        });
    }
}