<?php

namespace App\Http\Controllers;

use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Models\PrestasiSiklus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HASIL & RANKING (SISI ADMINISTRATOR)
    |--------------------------------------------------------------------------
    | Hanya menampilkan madrasah yang siklus prestasinya untuk periode terkait
    | sudah berstatus FINISHED (sudah selesai dinilai asesor & difinalisasi).
    | Madrasah yang masih OPEN/SUBMITTED/ASSESSMENT tidak relevan untuk
    | ranking karena nilai belum final.
    */
    public function index(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();

        /*
        |--------------------------------------------------------------------------
        | DAFTAR PERIODE UNTUK DROPDOWN
        |--------------------------------------------------------------------------
        | Cuma periode yang benar-benar punya madrasah FINISHED yang ditampilkan
        | di dropdown -- supaya tidak ada pilihan periode yang ranking-nya
        | pasti kosong.
        */
        $daftarPeriode = PrestasiSiklus::where('status', PrestasiSiklus::FINISHED)
            ->select('periode')
            ->distinct()
            ->pluck('periode');

        if (! $daftarPeriode->contains($periode)) {
            $daftarPeriode->push($periode);
        }

        $daftarPeriode = $daftarPeriode->sortDesc()->values();

        $madrasahIdsFinished = PrestasiSiklus::where('periode', $periode)
            ->where('status', PrestasiSiklus::FINISHED)
            ->pluck('madrasah_id');

        /*
        |--------------------------------------------------------------------------
        | Total nilai akhir per madrasah, diagregasi langsung di database
        | (SUM), bukan di-loop di PHP.
        |--------------------------------------------------------------------------
        */
        $totals = DB::table('penilaian_prestasis')
            ->join('prestasi_siswas', 'prestasi_siswas.id', '=', 'penilaian_prestasis.prestasi_siswa_id')
            ->where('penilaian_prestasis.status', 'completed')
            ->whereIn('prestasi_siswas.madrasah_id', $madrasahIdsFinished)
            ->where('prestasi_siswas.periode', $periode)
            ->groupBy('prestasi_siswas.madrasah_id')
            ->selectRaw('
                prestasi_siswas.madrasah_id,
                SUM(penilaian_prestasis.nilai_akhir) as total_nilai_akhir,
                COUNT(*) as jumlah_dinilai
            ')
            ->get()
            ->keyBy('madrasah_id');

        /*
        |--------------------------------------------------------------------------
        | FILTER JENJANG (toggle) — kosong = semua jenjang digabung jadi satu
        | ranking, diisi = ranking dihitung ulang hanya di dalam jenjang itu.
        |--------------------------------------------------------------------------
        */
        $jenjangFilter = $request->query('jenjang');

        $daftarJenjang = Madrasah::whereIn('id', $madrasahIdsFinished)
            ->select('jenjang_madrasah')
            ->distinct()
            ->orderBy('jenjang_madrasah')
            ->pluck('jenjang_madrasah');

        $ranking = Madrasah::whereIn('id', $madrasahIdsFinished)
            ->when($jenjangFilter, function ($q) use ($jenjangFilter) {
                $q->where('jenjang_madrasah', $jenjangFilter);
            })
            ->get()
            ->map(function ($madrasah) use ($totals) {
                $t = $totals->get($madrasah->id);

                return (object) [
                    'id'               => $madrasah->id,
                    'nama_madrasah'    => $madrasah->nama_madrasah,
                    'npsn'             => $madrasah->npsn,
                    'jenjang_madrasah' => $madrasah->jenjang_madrasah,
                    'kota'             => $madrasah->kota,
                    'total_nilai'      => round($t->total_nilai_akhir ?? 0, 2),
                    'jumlah_dinilai'   => $t->jumlah_dinilai ?? 0,
                ];
            })
            ->sortByDesc('total_nilai')
            ->values()
            ->map(function ($item, $index) {
                $item->peringkat = $index + 1;
                return $item;
            });

        $breadcrumb = breadcrumb([
            'Hasil & Ranking'
        ]);

        return view('ranking.index', compact(
            'ranking',
            'daftarJenjang',
            'jenjangFilter',
            'daftarPeriode',
            'periode',
            'breadcrumb'
        ));
    }
}