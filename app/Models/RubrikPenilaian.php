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
}