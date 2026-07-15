<?php

namespace App\Http\Controllers;

use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Models\PrestasiSiklus;
use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HASIL PENILAIAN (SISI MADRASAH)
    |--------------------------------------------------------------------------
    | Menampilkan data prestasi milik madrasah yang sedang login beserta
    | nilai dari asesor (kalau sudah dinilai), total nilai akhir, dan posisi
    | peringkat mereka -- peringkat hanya dihitung kalau siklus periode yang
    | dilihat sudah FINISHED, dibandingkan sesama jenjang madrasah.
    |
    | PENTING: halaman ini TIDAK selalu mengunci ke periode aktif global.
    | Begitu admin membuka periode baru, hasil FINISHED periode sebelumnya
    | tidak boleh langsung hilang dari pandangan Madrasah -- makanya ada
    | ?periode= supaya mereka bisa lihat riwayat periode manapun yang
    | pernah mereka jalani, dan defaultnya ke periode aktif kalau tidak
    | memilih apa-apa.
    */
    public function index(Request $request)
    {
        $madrasah = auth()->user()->madrasah;

        $periodeDilihat = $request->integer('periode') ?: PeriodeAktif::aktif();

        // Cari siklus untuk periode yang sedang dilihat. TIDAK pakai
        // prestasiSiklusAktif() di sini karena method itu firstOrCreate ke
        // periode aktif GLOBAL -- kalau user memilih periode lama lewat
        // dropdown, kita tidak mau malah bikin baris baru untuk periode itu.
        $siklus = $madrasah->prestasiSiklus()
            ->where('periode', $periodeDilihat)
            ->first();

        // Kalau memang belum pernah ada siklus untuk periode yang diminta
        // (madrasah belum pernah aktif di tahun itu), tampilkan sebagai OPEN
        // kosong sekadar untuk ditampilkan -- BUKAN disimpan ke database.
        if (! $siklus) {
            $siklus = new PrestasiSiklus([
                'periode' => $periodeDilihat,
                'status'  => PrestasiSiklus::OPEN,
            ]);
        }

        // Daftar periode yang pernah dijalani madrasah ini, buat dropdown.
        $daftarPeriodeTersedia = $madrasah->prestasiSiklus()
            ->orderByDesc('periode')
            ->pluck('periode');

        $daftarPrestasi = PrestasiSiswa::visible()
            ->where('periode', $periodeDilihat)
            ->with('penilaianPrestasi')
            ->orderByDesc('waktu_kegiatan')
            ->get()
            ->map(function ($prestasi) {
                $penilaian = $prestasi->penilaianPrestasi;

                return (object) [
                    'id'                 => $prestasi->id,
                    'nama_kegiatan'      => $prestasi->nama_kegiatan,
                    'bidang_prestasi'    => $prestasi->bidang_prestasi,
                    'tingkat'            => $prestasi->tingkat,
                    'skor'               => $prestasi->skor,
                    'metode_pelaksanaan' => $prestasi->metode_pelaksanaan,
                    'persentase'         => $penilaian->persentase ?? null,
                    'nilai_akhir'        => $penilaian->nilai_akhir ?? null,
                    'sudah_dinilai'      => $penilaian !== null && $penilaian->status === 'completed',
                ];
            });

        $totalNilaiAkhir = round($daftarPrestasi->sum('nilai_akhir'), 2);

        /*
        |--------------------------------------------------------------------------
        | PERINGKAT — hanya dihitung kalau siklus PERIODE YANG DILIHAT sudah
        | FINISHED, dibandingkan hanya dengan madrasah lain di jenjang yang
        | sama, dan hanya nilai dari periode yang sama pula.
        |--------------------------------------------------------------------------
        */
        $peringkat = null;
        $totalPesertaJenjang = null;

        if ($siklus->status === PrestasiSiklus::FINISHED) {

            $madrasahIdsFinished = PrestasiSiklus::where('periode', $periodeDilihat)
                ->where('status', PrestasiSiklus::FINISHED)
                ->pluck('madrasah_id');

            $madrasahIdsJenjangSama = Madrasah::whereIn('id', $madrasahIdsFinished)
                ->where('jenjang_madrasah', $madrasah->jenjang_madrasah)
                ->pluck('id');

            $totals = DB::table('penilaian_prestasis')
                ->join('prestasi_siswas', 'prestasi_siswas.id', '=', 'penilaian_prestasis.prestasi_siswa_id')
                ->where('penilaian_prestasis.status', 'completed')
                ->whereIn('prestasi_siswas.madrasah_id', $madrasahIdsJenjangSama)
                ->where('prestasi_siswas.periode', $periodeDilihat)
                ->groupBy('prestasi_siswas.madrasah_id')
                ->selectRaw('prestasi_siswas.madrasah_id, SUM(penilaian_prestasis.nilai_akhir) as total_nilai_akhir')
                ->get()
                ->sortByDesc('total_nilai_akhir')
                ->values();

            $totalPesertaJenjang = $totals->count();

            $posisi = $totals->search(function ($t) use ($madrasah) {
                return $t->madrasah_id === $madrasah->id;
            });

            $peringkat = $posisi !== false ? $posisi + 1 : null;
        }

        $breadcrumb = breadcrumb([
            'Hasil Penilaian'
        ]);

        return view('hasil.index', compact(
            'siklus',
            'daftarPrestasi',
            'totalNilaiAkhir',
            'peringkat',
            'totalPesertaJenjang',
            'periodeDilihat',
            'daftarPeriodeTersedia',
            'breadcrumb'
        ));
    }
}