<?php

namespace App\Http\Controllers;

use App\Models\AssignAsesor;
use App\Models\PeriodeAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringAsesorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MONITORING ASESOR (SISI ADMINISTRATOR)
    |--------------------------------------------------------------------------
    | Rangkuman progress SEMUA asesor sekaligus untuk satu periode --
    | melengkapi DashboardAsesorController yang cuma menampilkan progress
    | milik satu asesor yang sedang login.
    |
    | Prinsip desain: agregasi dihitung di database (bukan loop per-asesor
    | yang masing-masing query ke prestasi_siswas), supaya tetap ringan
    | walau jumlah asesor/madrasah bertambah banyak.
    */
    public function index(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();

        /*
        |--------------------------------------------------------------------------
        | DAFTAR PERIODE UNTUK DROPDOWN
        |--------------------------------------------------------------------------
        */
        $daftarPeriode = AssignAsesor::select('periode')
            ->distinct()
            ->pluck('periode');

        if (! $daftarPeriode->contains($periode)) {
            $daftarPeriode->push($periode);
        }

        $daftarPeriode = $daftarPeriode->sortDesc()->values();

        /*
        |--------------------------------------------------------------------------
        | SELURUH ASSIGNMENT UNTUK PERIODE INI
        |--------------------------------------------------------------------------
        */
        $assignments = AssignAsesor::where('periode', $periode)
            ->with('asesor')
            ->get();

        $madrasahIds = $assignments->pluck('madrasah_id')->unique()->values();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK PRESTASI PER MADRASAH -- SATU QUERY AGREGAT
        |--------------------------------------------------------------------------
        | Dihitung sekali di database untuk seluruh madrasah yang relevan,
        | BUKAN di-loop query satu-satu per asesor/madrasah.
        */
        $statistikPerMadrasah = collect();

        if ($madrasahIds->isNotEmpty()) {
            $statistikPerMadrasah = DB::table('prestasi_siswas')
                ->leftJoin('penilaian_prestasis', 'penilaian_prestasis.prestasi_siswa_id', '=', 'prestasi_siswas.id')
                ->whereIn('prestasi_siswas.madrasah_id', $madrasahIds)
                ->where('prestasi_siswas.periode', $periode)
                ->groupBy('prestasi_siswas.madrasah_id')
                ->selectRaw('
                    prestasi_siswas.madrasah_id,
                    COUNT(DISTINCT prestasi_siswas.id) as total_prestasi,
                    COUNT(DISTINCT penilaian_prestasis.id) as sudah_dinilai
                ')
                ->get()
                ->keyBy('madrasah_id');
        }

        /*
        |--------------------------------------------------------------------------
        | RANGKUM PER ASESOR
        |--------------------------------------------------------------------------
        */
        $daftarAsesor = $assignments
            ->groupBy('asesor_id')
            ->map(function ($assignmentsAsesor) use ($statistikPerMadrasah) {

                $asesor = $assignmentsAsesor->first()->asesor;

                $totalMadrasah = $assignmentsAsesor->count();
                $selesai       = $assignmentsAsesor->where('status', 'completed')->count();
                $dikerjakan    = $assignmentsAsesor->where('status', 'in_progress')->count();
                $belumMulai    = $assignmentsAsesor->whereIn('status', ['assigned', 'not_assigned'])->count();

                $totalPrestasi = 0;
                $sudahDinilai  = 0;

                foreach ($assignmentsAsesor as $assignment) {
                    $stat = $statistikPerMadrasah->get($assignment->madrasah_id);
                    $totalPrestasi += $stat->total_prestasi ?? 0;
                    $sudahDinilai  += $stat->sudah_dinilai ?? 0;
                }

                return (object) [
                    'asesor_id'      => $asesor->id ?? null,
                    'nama_asesor'    => $asesor->nama ?? '-',
                    'total_madrasah' => $totalMadrasah,
                    'selesai'        => $selesai,
                    'dikerjakan'     => $dikerjakan,
                    'belum_mulai'    => $belumMulai,
                    'total_prestasi' => $totalPrestasi,
                    'sudah_dinilai'  => $sudahDinilai,
                    'progress'       => $totalPrestasi > 0 ? round($sudahDinilai / $totalPrestasi * 100) : 0,
                ];
            })
            ->sortBy('progress')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RINGKASAN KESELURUHAN
        |--------------------------------------------------------------------------
        */
        $totalAsesor          = $daftarAsesor->count();
        $totalMadrasahDinilai = $assignments->count();
        $asesorSelesaiSemua   = $daftarAsesor->filter(fn ($a) => $a->total_madrasah > 0 && $a->belum_mulai === 0 && $a->dikerjakan === 0)->count();
        $asesorBelumMulai     = $daftarAsesor->filter(fn ($a) => $a->selesai === 0 && $a->dikerjakan === 0 && $a->total_madrasah > 0)->count();

        $totalPrestasiKeseluruhan = $daftarAsesor->sum('total_prestasi');
        $sudahDinilaiKeseluruhan  = $daftarAsesor->sum('sudah_dinilai');
        $progresKeseluruhan       = $totalPrestasiKeseluruhan > 0
            ? round($sudahDinilaiKeseluruhan / $totalPrestasiKeseluruhan * 100)
            : 0;

        $breadcrumb = breadcrumb([
            'Monitoring Asesor'
        ]);

        return view('monitoring-asesor.index', compact(
            'periode',
            'daftarPeriode',
            'daftarAsesor',
            'totalAsesor',
            'totalMadrasahDinilai',
            'asesorSelesaiSemua',
            'asesorBelumMulai',
            'progresKeseluruhan',
            'breadcrumb'
        ));
    }
}