<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPenilaianExport;
use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportCenterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR JENJANG
    |--------------------------------------------------------------------------
    | ASUMSI: nilai kolom Madrasah.jenjang_madrasah persis salah satu dari
    | daftar ini (RA/MI/MTs/MA), mengikuti pola tab jenjang yang sudah
    | dipakai di halaman lain (dashboard superadmin, dsb). Kalau nilai
    | aslinya beda (misal "MTS" huruf besar semua), sesuaikan di sini saja.
    |--------------------------------------------------------------------------
    */
    private const DAFTAR_JENJANG = ['RA', 'MI', 'MTs', 'MA'];

    /*
    |--------------------------------------------------------------------------
    | INDEX — halaman Export Center
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $breadcrumb = breadcrumb([
            'Export Center',
        ]);

        // Daftar periode diambil dari data prestasi yang benar-benar ada,
        // supaya tidak menampilkan pilihan periode yang datanya kosong.
        $daftarPeriode = PrestasiSiswa::query()
            ->select('periode')
            ->whereNotNull('periode')
            ->distinct()
            ->orderByDesc('periode')
            ->pluck('periode');

        $periodeAktif = PeriodeAktif::aktif();

        // Semua madrasah + jenjangnya diambil sekaligus di awal, dipakai
        // buat filter select Madrasah yang difilter pakai JS berdasarkan
        // Jenjang yang dipilih -- supaya tidak perlu request AJAX terpisah.
        // "Terdaftar" = madrasah punya akun user (relasi Madrasah::users())
        // dengan is_active = true.
        $daftarMadrasah = Madrasah::orderBy('nama_madrasah')
            ->whereHas('users', fn ($q) => $q->where('is_active', true))
            ->get(['id', 'nama_madrasah', 'jenjang_madrasah']);

        return view('export-center.index', [
            'breadcrumb' => $breadcrumb,
            'daftarPeriode' => $daftarPeriode,
            'periodeAktif' => $periodeAktif,
            'daftarMadrasah' => $daftarMadrasah,
            'daftarJenjang' => self::DAFTAR_JENJANG,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT — Laporan Hasil Penilaian Asesor (2 sheet: Ringkasan + Detail)
    |--------------------------------------------------------------------------
    | periode & jenjang WAJIB (mengurangi resiko ekspor seluruh database
    | tanpa sengaja). madrasah_id OPSIONAL -- kosong berarti seluruh
    | madrasah pada jenjang yang dipilih.
    |--------------------------------------------------------------------------
    */
    public function exportLaporanPenilaian(Request $request)
    {
        $validated = $request->validate([
            'periode' => ['required', 'integer'],
            // ASUMSI nama tabel: madrasahs. Sesuaikan kalau beda.
            'jenjang' => ['required', 'in:' . implode(',', self::DAFTAR_JENJANG)],
            'madrasah_id' => ['nullable', 'integer', 'exists:madrasahs,id'],
        ]);

        $periode = (int) $validated['periode'];
        $jenjang = $validated['jenjang'];
        $madrasahId = isset($validated['madrasah_id']) ? (int) $validated['madrasah_id'] : null;

        $namaFile = 'Laporan-Penilaian-' . $jenjang . '-' . $periode . '.xlsx';

        return Excel::download(
            new LaporanPenilaianExport($periode, $jenjang, $madrasahId),
            $namaFile
        );
    }
}