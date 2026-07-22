<?php

namespace App\Http\Controllers;

use App\Models\AssignAsesor;
use App\Models\PrestasiSiswa;
use App\Models\RubrikPenilaian;
use Illuminate\Http\Request;

class DashboardAsesorController extends Controller
{
    private const URUTAN_BIDANG = [
        'Akademik',
        'Non Akademik',
        'Keagamaan',
        'GTK',
        'Lembaga',
    ];

    private const WARNA_BIDANG = [
        'Akademik'     => '#2563eb',
        'Non Akademik' => '#38bdf8',
        'Keagamaan'    => '#f59e0b',
        'GTK'          => '#8b5cf6',
        'Lembaga'      => '#94a3b8',
    ];

    private const WARNA_PERSENTASE = ['#1d4ed8', '#38bdf8', '#f59e0b', '#8b5cf6', '#10b981', '#94a3b8'];

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | SELURUH ASSIGNMENT MILIK ASESOR INI
        |--------------------------------------------------------------------------
        | TIDAK difilter periode aktif -- sama seperti AsesorController::index(),
        | supaya assignment yang belum completed dari periode sebelumnya tidak
        | hilang begitu admin membuka periode baru.
        */
        $assignments = AssignAsesor::where('asesor_id', auth()->id())
            ->with('madrasah')
            ->orderByDesc('periode')
            ->get();

        $totalMadrasah      = $assignments->count();
        $madrasahCompleted  = $assignments->where('status', 'completed')->count();
        $madrasahInProgress = $assignments->where('status', 'in_progress')->count();
        $madrasahBelumMulai = $assignments->whereIn('status', ['assigned', 'not_assigned'])->count();

        /*
        |--------------------------------------------------------------------------
        | SELURUH PRESTASI DARI SEMUA MADRASAH+PERIODE YANG DI-ASSIGN
        |--------------------------------------------------------------------------
        | Satu query gabungan (bukan query per-assignment di dalam loop) --
        | dipasangkan persis (madrasah_id, periode) per assignment, karena satu
        | asesor bisa punya assignment dari periode berbeda-beda untuk madrasah
        | yang berbeda pula.
        */
        $semuaPrestasi = collect();

        if ($assignments->isNotEmpty()) {
            $semuaPrestasi = PrestasiSiswa::where(function ($query) use ($assignments) {
                    foreach ($assignments as $assignment) {
                        $query->orWhere(function ($q) use ($assignment) {
                            $q->where('madrasah_id', $assignment->madrasah_id)
                                ->where('periode', $assignment->periode);
                        });
                    }
                })
                ->with('penilaianPrestasi')
                ->get();
        }

        $totalPrestasi      = $semuaPrestasi->count();
        $sudahDinilai       = $semuaPrestasi->filter(fn ($p) => $p->penilaianPrestasi !== null)->count();
        $belumDinilai       = $totalPrestasi - $sudahDinilai;
        $progresKeseluruhan = $totalPrestasi > 0 ? round($sudahDinilai / $totalPrestasi * 100) : 0;

        /*
        |--------------------------------------------------------------------------
        | DAFTAR MADRASAH + PROGRESS MASING-MASING
        |--------------------------------------------------------------------------
        */
        $daftarMadrasah = $assignments->map(function ($assignment) use ($semuaPrestasi) {
            $prestasiMadrasahIni = $semuaPrestasi->filter(function ($p) use ($assignment) {
                return $p->madrasah_id === $assignment->madrasah_id
                    && $p->periode == $assignment->periode;
            });

            $total = $prestasiMadrasahIni->count();
            $sudah = $prestasiMadrasahIni->filter(fn ($p) => $p->penilaianPrestasi !== null)->count();

            return (object) [
                'madrasah_id'    => $assignment->madrasah_id,
                'nama_madrasah'  => $assignment->madrasah->nama_madrasah ?? '-',
                'periode'        => $assignment->periode,
                'status'         => $assignment->status,
                'total_prestasi' => $total,
                'sudah_dinilai'  => $sudah,
                'progress'       => $total > 0 ? round($sudah / $total * 100) : 0,
            ];
        })->sortBy('progress')->values();

        /*
        |--------------------------------------------------------------------------
        | PROGRESS PENILAIAN PER BIDANG (grouped bar: total vs sudah dinilai)
        |--------------------------------------------------------------------------
        */
        $progresPerBidang = collect(self::URUTAN_BIDANG)
            ->map(function ($bidang) use ($semuaPrestasi) {
                $subset = $semuaPrestasi->where('bidang_prestasi', $bidang);
                $total  = $subset->count();
                $sudah  = $subset->filter(fn ($p) => $p->penilaianPrestasi !== null)->count();

                return [
                    'bidang' => $bidang,
                    'total'  => $total,
                    'sudah'  => $sudah,
                    'warna'  => self::WARNA_BIDANG[$bidang],
                ];
            })
            ->filter(fn ($row) => $row['total'] > 0)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | DISTRIBUSI PERSENTASE NILAI YANG SUDAH DIBERIKAN
        |--------------------------------------------------------------------------
        */
        $distribusiPersentase = $semuaPrestasi
            ->pluck('penilaianPrestasi')
            ->filter()
            ->groupBy('persentase')
            ->map(fn ($items, $persentase) => [
                'label'  => $persentase . '%',
                'jumlah' => $items->count(),
            ])
            ->sortKeys()
            ->values()
            ->map(function ($item, $index) {
                $item['warna'] = self::WARNA_PERSENTASE[$index] ?? '#cbd5e1';
                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | KECOCOKAN DENGAN RUBRIK JUKNIS (menggantikan tren 14 hari yang lama --
        | itu cuma laporan historis "berapa dinilai per hari", tidak actionable.
        | Ini lebih berguna: dari semua yang SUDAH Anda nilai, berapa yang
        | skornya cocok/beda/belum ada rubriknya -- dan yang "beda" itu
        | ditampilkan detailnya supaya bisa langsung dicek ulang.
        |--------------------------------------------------------------------------
        */
        $prestasiSudahDinilai = $semuaPrestasi->filter(fn ($p) => $p->penilaianPrestasi !== null);

        // Lookup nama madrasah dari data assignment yang sudah di-load (hindari
        // query tambahan per prestasi).
        $namaMadrasahById = $assignments->pluck('madrasah.nama_madrasah', 'madrasah_id');

        $kecocokanRubrik = $prestasiSudahDinilai->map(function ($p) use ($namaMadrasahById) {
            $hasil = RubrikPenilaian::statusKecocokan(
                bidangPrestasi: $p->bidang_prestasi,
                tingkat: $p->tingkat,
                juara: $p->juara,
                kategoriKegiatan: $p->kategori_kegiatan,
                metodePelaksanaan: $p->metode_pelaksanaan,
                kategoriPenyelenggara: $p->kategori_penyelenggara,
                tahun: (int) $p->periode,
                skorMadrasah: (float) ($p->skor ?? 0)
            );

            return (object) [
                'nama_kegiatan'  => $p->nama_kegiatan,
                'nama_madrasah'  => $namaMadrasahById->get($p->madrasah_id, '-'),
                'bidang'         => $p->bidang_prestasi,
                'status'         => $hasil['status'], // 'cocok' | 'tidak_cocok' | 'tidak_ada'
                'skor_madrasah'  => $p->skor,
                'skor_rubrik'    => $hasil['skor_rubrik'],
            ];
        });

        $totalDinilaiUntukRubrik = $kecocokanRubrik->count();
        $jumlahSesuaiRubrik      = $kecocokanRubrik->where('status', 'cocok')->count();
        $jumlahBedaRubrik        = $kecocokanRubrik->where('status', 'tidak_cocok')->count();
        $jumlahBelumAdaRubrik    = $kecocokanRubrik->where('status', 'tidak_ada')->count();

        $persenSesuaiRubrik = $totalDinilaiUntukRubrik > 0
            ? round($jumlahSesuaiRubrik / $totalDinilaiUntukRubrik * 100)
            : 0;

        // Daftar yang BEDA dari rubrik -- paling actionable, batasi 8 biar
        // ringkas, urutkan dari selisih terbesar supaya yang paling
        // mencolok muncul duluan.
        $daftarBedaRubrik = $kecocokanRubrik
            ->where('status', 'tidak_cocok')
            ->map(function ($item) {
                $item->selisih = abs($item->skor_madrasah - $item->skor_rubrik);
                return $item;
            })
            ->sortByDesc('selisih')
            ->take(8)
            ->values();

        $kecocokanRubrikRingkasan = [
            'total'          => $totalDinilaiUntukRubrik,
            'sesuai'         => $jumlahSesuaiRubrik,
            'beda'           => $jumlahBedaRubrik,
            'belum_ada'      => $jumlahBelumAdaRubrik,
            'persen_sesuai'  => $persenSesuaiRubrik,
        ];

        /*
        |--------------------------------------------------------------------------
        | INSIGHT — rule-based sederhana
        |--------------------------------------------------------------------------
        */
        $insight = $this->buildInsight(
            $totalMadrasah,
            $madrasahCompleted,
            $progresPerBidang,
            $daftarMadrasah,
            $semuaPrestasi
        );

        return view('dashboard.asesor', compact(
            'totalMadrasah',
            'madrasahCompleted',
            'madrasahInProgress',
            'madrasahBelumMulai',
            'totalPrestasi',
            'sudahDinilai',
            'belumDinilai',
            'progresKeseluruhan',
            'daftarMadrasah',
            'progresPerBidang',
            'distribusiPersentase',
            'kecocokanRubrikRingkasan',
            'daftarBedaRubrik',
            'insight'
        ));
    }

    private function buildInsight(
        int $totalMadrasah,
        int $madrasahCompleted,
        $progresPerBidang,
        $daftarMadrasah,
        $semuaPrestasi
    ): array {
        $insight = [];

        if ($totalMadrasah > 0) {
            $insight[] = [
                'icon' => 'bi-clipboard-check',
                'text' => "Anda sudah menyelesaikan penilaian <strong>{$madrasahCompleted} dari {$totalMadrasah}</strong> madrasah yang ditugaskan.",
            ];
        }

        $bidangPalingTersisa = $progresPerBidang
            ->map(fn ($row) => $row + ['sisa' => $row['total'] - $row['sudah']])
            ->sortByDesc('sisa')
            ->first();

        if ($bidangPalingTersisa && $bidangPalingTersisa['sisa'] > 0) {
            $insight[] = [
                'icon' => 'bi-hourglass-split',
                'text' => "Bidang <strong>{$bidangPalingTersisa['bidang']}</strong> masih memiliki <strong>{$bidangPalingTersisa['sisa']}</strong> prestasi yang belum dinilai.",
            ];
        }

        $madrasahPalingTertinggal = $daftarMadrasah
            ->where('status', '!=', 'completed')
            ->where('total_prestasi', '>', 0)
            ->sortBy('progress')
            ->first();

        if ($madrasahPalingTertinggal) {
            $insight[] = [
                'icon' => 'bi-flag',
                'text' => "Madrasah <strong>{$madrasahPalingTertinggal->nama_madrasah}</strong> baru <strong>{$madrasahPalingTertinggal->progress}%</strong> dinilai, perlu diprioritaskan.",
            ];
        }

        $rataPersentase = $semuaPrestasi->pluck('penilaianPrestasi')->filter()->avg('persentase');

        if ($rataPersentase !== null) {
            $insight[] = [
                'icon' => 'bi-bar-chart',
                'text' => 'Rata-rata persentase nilai yang Anda berikan adalah <strong>' . round($rataPersentase) . '%</strong>.',
            ];
        }

        return $insight;
    }
}