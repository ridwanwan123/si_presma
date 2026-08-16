<?php

namespace App\Http\Controllers;

use App\Models\AssignAsesor;
use App\Models\PrestasiSiswa;
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

    private const WARNA_PERSENTASE = ['#2563eb', '#8b5cf6', '#10b981', '#f59e0b', '#38bdf8', '#94a3b8'];
    private const WARNA_NOL_PERSEN = '#ef4444';

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
                'persentase' => (int) $persentase,
                'label'      => $persentase . '%',
                'jumlah'     => $items->count(),
            ])
            // Urutkan dari persentase TERTINGGI dulu supaya warna ke-0 (paling
            // menarik) jatuh ke nilai tertinggi, bukan sekadar urutan array.
            ->sortByDesc('persentase')
            ->values()
            ->map(function ($item, $index) {
                $item['warna'] = $item['persentase'] === 0
                    ? self::WARNA_NOL_PERSEN
                    : (self::WARNA_PERSENTASE[$index] ?? '#cbd5e1');
                return $item;
            })
            // Tampilan tetap ascending (0% -> 100%); warna sudah ditentukan
            // di atas jadi urutan tampil ini tidak memengaruhi pewarnaan.
            ->sortBy('persentase')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RINGKASAN VERIFIKASI — DIAKUI vs TIDAK DIAKUI
        |--------------------------------------------------------------------------
        | Menggantikan "Kecocokan dengan Rubrik Juknis" yang lama -- widget itu
        | dulu berguna karena skor diketik manual oleh madrasah sehingga sering
        | meleset dari rubrik. Sekarang skor sudah dihitung otomatis mengikuti
        | rubrik sejak awal, jadi membandingkan skor dengan rubrik lagi di sini
        | cuma membandingkan sesuatu dengan dirinya sendiri (tidak actionable).
        |
        | Widget ini menampilkan hal yang justru MASIH murni judgment asesor:
        | keputusan 'diakui' (checkbox di modal "Beri Nilai") -- bukan hasil
        | hitungan sistem. Daftar yang "Tidak Diakui" dibatasi 8 baris terbaru
        | supaya ringkas, sisanya tetap bisa dicek di halaman Madrasah > Detail.
        |--------------------------------------------------------------------------
        */
        $namaMadrasahById = $assignments->pluck('madrasah.nama_madrasah', 'madrasah_id');

        $prestasiSudahDinilai = $semuaPrestasi->filter(fn ($p) => $p->penilaianPrestasi !== null);

        $totalSudahDinilai = $prestasiSudahDinilai->count();
        $jumlahDiakui      = $prestasiSudahDinilai->where('diakui', true)->count();
        $jumlahTidakDiakui = $totalSudahDinilai - $jumlahDiakui;

        $verifikasiRingkasan = [
            'total'         => $totalSudahDinilai,
            'diakui'        => $jumlahDiakui,
            'tidak_diakui'  => $jumlahTidakDiakui,
            'persen_diakui' => $totalSudahDinilai > 0 ? round($jumlahDiakui / $totalSudahDinilai * 100) : 0,
        ];

        $daftarTidakDiakui = $prestasiSudahDinilai
            ->where('diakui', false)
            ->sortByDesc(fn ($p) => $p->penilaianPrestasi->updated_at)
            ->take(2)
            ->map(fn ($p) => (object) [
                'nama_kegiatan' => $p->nama_kegiatan,
                'nama_madrasah' => $namaMadrasahById->get($p->madrasah_id, '-'),
                'catatan'       => $p->penilaianPrestasi->catatan,
            ])
            ->values();

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
            'verifikasiRingkasan',
            'daftarTidakDiakui',
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