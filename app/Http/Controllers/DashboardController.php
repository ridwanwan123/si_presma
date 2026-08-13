<?php

namespace App\Http\Controllers;

use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Models\PrestasiSiklus;
use App\Models\PrestasiSiswa;
use App\Services\PenguranganPoinService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

    private const URUTAN_TINGKAT = [
        'Kabupaten/Kota',
        'Provinsi',
        'Nasional',
        'Internasional',
    ];

    private const URUTAN_JENJANG = ['RA', 'MI', 'MTs', 'MA'];

    private const WARNA_JUARA = ['#1d4ed8', '#38bdf8', '#f59e0b', '#8b5cf6', '#10b981', '#94a3b8'];

    private const NAMA_BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    private const TOP_LEMBAGA_DITAMPILKAN = 10;

    public function __construct(
        private PenguranganPoinService $penguranganPoinService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD SUPERADMIN — SINGLE PERIODE (TIDAK ADA PERBANDINGAN ANTAR
    | TAHUN). Semua komponen di bawah menampilkan gambaran SATU periode yang
    | sedang dipilih, di-scope oleh filter Jenjang/Status/Kota yang sama.
    |--------------------------------------------------------------------------
    | Filter Jenjang berlaku KONSISTEN ke seluruh komponen -- termasuk yang
    | "built-in" memecah per jenjang (Matrix Jenjang x Bidang & Hasil
    | Ranking) -- supaya tidak ada pengecualian yang membingungkan orang
    | yang lagi filter.
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();
        [$jenjangFilter, $statusFilter, $kotaFilter] = $this->bacaFilter($request);

        $madrasahIdsStatus = $this->madrasahIdsByStatus($statusFilter);

        $daftarPeriode = $this->daftarPeriode();
        $opsiFilter = $this->opsiFilter();

        $ringkasanPeriode = $this->ringkasanPeriode($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);
        $perbandinganTingkat = $this->perbandinganTingkat($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);
        $matrixJenjangBidang = $this->matrixJenjangBidang($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);
        $juaraPerJenjangBidang = $this->juaraPerJenjangBidang($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);

        $totalPrestasi = $ringkasanPeriode['total_prestasi'];

        $komposisiBidang = $this->komposisiBidang($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus, $totalPrestasi);
        $komposisiJuara = $this->komposisiJuara($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus, $totalPrestasi);
        $sebaranTingkat = $this->sebaranTingkat($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);
        $komposisiKategori = $this->komposisiKategori($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus, $totalPrestasi);
        $komposisiMetode = $this->komposisiMetode($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus, $totalPrestasi);
        $distribusiBulan = $this->distribusiBulan($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);
        $topLembaga = $this->topLembaga($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus, $totalPrestasi);

        $breadcrumb = breadcrumb(['Dashboard']);

        return view('dashboard.index', compact(
            'periode',
            'daftarPeriode',
            'jenjangFilter',
            'statusFilter',
            'kotaFilter',
            'opsiFilter',
            'ringkasanPeriode',
            'perbandinganTingkat',
            'matrixJenjangBidang',
            'juaraPerJenjangBidang',
            'totalPrestasi',
            'komposisiBidang',
            'komposisiJuara',
            'sebaranTingkat',
            'komposisiKategori',
            'komposisiMetode',
            'distribusiBulan',
            'topLembaga',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER: BACA DARI REQUEST & OPSI DROPDOWN
    |--------------------------------------------------------------------------
    */
    private function bacaFilter(Request $request): array
    {
        return [
            $request->query('jenjang') ?: null,
            $request->query('status') ?: null,
            $request->query('kota') ?: null,
        ];
    }

    private function opsiFilter(): array
    {
        return [
            'jenjang' => Madrasah::whereNotNull('jenjang_madrasah')
                ->distinct()
                ->orderBy('jenjang_madrasah')
                ->pluck('jenjang_madrasah'),

            'status' => Madrasah::whereNotNull('status_madrasah')
                ->distinct()
                ->orderBy('status_madrasah')
                ->pluck('status_madrasah'),

            'kota' => Madrasah::whereNotNull('kota')
                ->distinct()
                ->orderBy('kota')
                ->pluck('kota'),
        ];
    }

    private function daftarPeriode(): Collection
    {
        $daftarPeriode = PrestasiSiswa::visible()
            ->select('periode')
            ->distinct()
            ->pluck('periode');

        if (! $daftarPeriode->contains(PeriodeAktif::aktif())) {
            $daftarPeriode->push(PeriodeAktif::aktif());
        }

        return $daftarPeriode->sortDesc()->values();
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR ID MADRASAH SESUAI STATUS (Negeri/Swasta)
    |--------------------------------------------------------------------------
    | null = filter status tidak aktif. Dihitung SEKALI per request, dipakai
    | berulang oleh semua komponen lain lewat whereIn('madrasah_id', ...).
    |--------------------------------------------------------------------------
    */
    private function madrasahIdsByStatus(?string $statusFilter): ?Collection
    {
        if (! $statusFilter) {
            return null;
        }

        return Madrasah::where('status_madrasah', $statusFilter)->pluck('id');
    }

    /**
     * Terapkan filter Jenjang/Status/Kota ke query builder Madrasah (dipakai
     * baik untuk query Madrasah langsung maupun di dalam whereHas('madrasah')).
     */
    private function terapkanFilterMadrasah(
        Builder $query,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus
    ): Builder {
        return $query
            ->when($jenjangFilter, fn ($q) => $q->where('jenjang_madrasah', $jenjangFilter))
            ->when($kotaFilter, fn ($q) => $q->where('kota', $kotaFilter))
            ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('id', $madrasahIdsStatus));
    }

    /**
     * Query dasar "prestasi diakui pada periode X, sesuai filter" -- dipakai
     * berulang oleh komponen 5-11. Selalu builder BARU tiap dipanggil.
     */
    private function basePrestasiQuery(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus
    ): Builder {
        return PrestasiSiswa::visible()
            ->where('diakui', true)
            ->where('periode', $periode)
            ->whereHas('madrasah', fn ($q) => $this->terapkanFilterMadrasah(
                $q,
                $jenjangFilter,
                $kotaFilter,
                $madrasahIdsStatus
            ));
    }

    /*
    |--------------------------------------------------------------------------
    | 1. RINGKASAN PERIODE BERJALAN
    |--------------------------------------------------------------------------
    */
    private function ringkasanPeriode(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus
    ): array {
        $totalPrestasi = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)->count();

        $madrasahAktif = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)
            ->distinct('madrasah_id')
            ->count('madrasah_id');

        $totalMadrasahTerdaftar = $this->terapkanFilterMadrasah(
            Madrasah::query(),
            $jenjangFilter,
            $kotaFilter,
            $madrasahIdsStatus
        )->count();

        $madrasahFinished = PrestasiSiklus::where('periode', $periode)
            ->where('status', PrestasiSiklus::FINISHED)
            ->whereHas('madrasah', fn ($q) => $this->terapkanFilterMadrasah(
                $q,
                $jenjangFilter,
                $kotaFilter,
                $madrasahIdsStatus
            ))
            ->count();

        return [
            'total_prestasi'           => $totalPrestasi,
            'madrasah_aktif'           => $madrasahAktif,
            'total_madrasah_terdaftar' => $totalMadrasahTerdaftar,
            'madrasah_finished'        => $madrasahFinished,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 2. PERBANDINGAN PRESTASI PER TINGKAT (satu periode, tanpa perbandingan
    |    tahun) -- selalu 4 baris walau sebagian nilainya 0.
    |--------------------------------------------------------------------------
    */
    private function perbandinganTingkat(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus
    ): Collection {
        $rows = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)
            ->groupBy('tingkat')
            ->selectRaw('tingkat, COUNT(*) as jumlah')
            ->pluck('jumlah', 'tingkat');

        return collect(self::URUTAN_TINGKAT)->map(fn ($tingkat) => [
            'tingkat' => $tingkat,
            'jumlah'  => (int) ($rows[$tingkat] ?? 0),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. MATRIX TOTAL PRESTASI PER JENJANG x BIDANG
    |--------------------------------------------------------------------------
    | Satu tabel kompak: baris = 5 bidang, kolom = jenjang (RA/MI/MTs/MA,
    | atau cuma 1 kolom kalau sedang difilter ke satu jenjang), + baris &
    | kolom Total.
    |--------------------------------------------------------------------------
    */
    private function matrixJenjangBidang(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus
    ): array {
        $daftarJenjang = $jenjangFilter ? collect([$jenjangFilter]) : collect(self::URUTAN_JENJANG);

        $rows = DB::table('prestasi_siswas')
            ->join('madrasahs', 'madrasahs.id', '=', 'prestasi_siswas.madrasah_id')
            ->where('prestasi_siswas.diakui', true)
            ->where('prestasi_siswas.periode', $periode)
            ->when($jenjangFilter, fn ($q) => $q->where('madrasahs.jenjang_madrasah', $jenjangFilter))
            ->when($kotaFilter, fn ($q) => $q->where('madrasahs.kota', $kotaFilter))
            ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('madrasahs.id', $madrasahIdsStatus))
            ->groupBy('madrasahs.jenjang_madrasah', 'prestasi_siswas.bidang_prestasi')
            ->selectRaw('
                madrasahs.jenjang_madrasah as jenjang,
                prestasi_siswas.bidang_prestasi as bidang,
                COUNT(*) as jumlah
            ')
            ->get();

        $matrix = collect(self::URUTAN_BIDANG)->map(function ($bidang) use ($rows, $daftarJenjang) {
            $perJenjang = $daftarJenjang->mapWithKeys(function ($jenjang) use ($rows, $bidang) {
                $match = $rows->first(fn ($r) => $r->jenjang === $jenjang && $r->bidang === $bidang);

                return [$jenjang => (int) ($match->jumlah ?? 0)];
            });

            return [
                'bidang'      => $bidang,
                'per_jenjang' => $perJenjang,
                'total'       => $perJenjang->sum(),
            ];
        });

        $totalPerJenjang = $daftarJenjang->mapWithKeys(fn ($jenjang) => [
            $jenjang => $matrix->sum(fn ($row) => $row['per_jenjang'][$jenjang]),
        ]);

        return [
            'daftar_jenjang'    => $daftarJenjang,
            'matrix'            => $matrix,
            'total_per_jenjang' => $totalPerJenjang,
            'total_keseluruhan' => $totalPerJenjang->sum(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 4. HASIL & RANKING PER JENJANG x BIDANG (PREVIEW TOP 3)
    |--------------------------------------------------------------------------
    | Preview ringkas (Juara 1-3 tiap kombinasi jenjang x bidang) supaya
    | dashboard tetap ringkas -- ranking lengkap ada di halaman Hasil &
    | Ranking. Hanya madrasah yang penilaiannya SUDAH difinalisasi (status
    | FINISHED) yang dihitung, konsisten dengan Ranking Live. Formula
    | potongan nilai per bidang memakai PenguranganPoinService yang sama
    | seperti Ranking Live & Arsip.
    |--------------------------------------------------------------------------
    */
    private function juaraPerJenjangBidang(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus
    ): Collection {
        $daftarJenjang = $jenjangFilter ? collect([$jenjangFilter]) : collect(self::URUTAN_JENJANG);

        $madrasahIdsFinished = PrestasiSiklus::where('periode', $periode)
            ->where('status', PrestasiSiklus::FINISHED)
            ->pluck('madrasah_id');

        $madrasahs = $this->terapkanFilterMadrasah(
            Madrasah::whereIn('id', $madrasahIdsFinished),
            $jenjangFilter,
            $kotaFilter,
            $madrasahIdsStatus
        )->get(['id', 'nama_madrasah', 'jenjang_madrasah']);

        $rows = $madrasahs->isEmpty()
            ? collect()
            : DB::table('penilaian_prestasis')
                ->join('prestasi_siswas', 'prestasi_siswas.id', '=', 'penilaian_prestasis.prestasi_siswa_id')
                ->where('penilaian_prestasis.status', 'completed')
                ->where('prestasi_siswas.diakui', true)
                ->where('prestasi_siswas.periode', $periode)
                ->whereIn('prestasi_siswas.madrasah_id', $madrasahs->pluck('id'))
                ->groupBy('prestasi_siswas.madrasah_id', 'prestasi_siswas.bidang_prestasi')
                ->selectRaw('
                    prestasi_siswas.madrasah_id,
                    prestasi_siswas.bidang_prestasi,
                    SUM(penilaian_prestasis.nilai_akhir) as total_nilai
                ')
                ->get()
                ->groupBy('madrasah_id');

        $dataLengkap = $this->dataLengkapPerMadrasah($madrasahs, $rows, $periode);

        $grid = collect();

        foreach ($daftarJenjang as $jenjang) {
            $dataJenjang = $dataLengkap->where('jenjang_madrasah', $jenjang);

            foreach (self::URUTAN_BIDANG as $bidang) {
                $top3 = $dataJenjang
                    ->map(fn ($item) => (object) [
                        'nama_madrasah' => $item->nama_madrasah,
                        'nilai_akhir'   => (int) round($item->per_bidang[$bidang]['nilai_akhir']),
                    ])
                    ->filter(fn ($row) => $row->nilai_akhir > 0)
                    ->sortByDesc('nilai_akhir')
                    ->values()
                    ->take(3)
                    ->map(function ($row, $index) {
                        $row->peringkat = $index + 1;
                        return $row;
                    });

                $grid->push([
                    'jenjang' => $jenjang,
                    'bidang'  => $bidang,
                    'top3'    => $top3,
                ]);
            }
        }

        return $grid;
    }

    /**
     * Nilai per bidang SETELAH potongan, untuk tiap madrasah -- dipakai
     * khusus oleh juaraPerJenjangBidang(). Formula sama persis dengan
     * RankingController::hitungRankingPerBidang() supaya juara yang
     * ditampilkan di dashboard selalu konsisten dengan halaman Ranking Live.
     */
    private function dataLengkapPerMadrasah(Collection $madrasahs, Collection $rows, int $periode): Collection
    {
        return $madrasahs->map(function ($madrasah) use ($rows, $periode) {
            $barisBidang = $rows->get($madrasah->id, collect());

            $nilaiPerBidang = collect(self::URUTAN_BIDANG)->mapWithKeys(function ($bidang) use ($barisBidang) {
                $match = $barisBidang->first(fn ($r) => $r->bidang_prestasi === $bidang);

                return [$bidang => (float) ($match->total_nilai ?? 0)];
            })->toArray();

            $hasilPotongan = $this->penguranganPoinService->hitungSetelahPotonganPerBidang(
                $madrasah->id,
                $periode,
                $nilaiPerBidang
            );

            return (object) [
                'madrasah_id'      => $madrasah->id,
                'nama_madrasah'    => $madrasah->nama_madrasah,
                'jenjang_madrasah' => $madrasah->jenjang_madrasah,
                'per_bidang'       => $hasilPotongan['per_bidang'],
            ];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | 5. KOMPOSISI BIDANG PRESTASI (donut, agregat sistem)
    |--------------------------------------------------------------------------
    */
    private function komposisiBidang(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus,
        int $totalPrestasi
    ): Collection {
        $rows = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)
            ->groupBy('bidang_prestasi')
            ->selectRaw('bidang_prestasi as bidang, COUNT(*) as jumlah')
            ->pluck('jumlah', 'bidang');

        return collect(self::URUTAN_BIDANG)
            ->map(fn ($bidang) => [
                'label'  => $bidang,
                'jumlah' => (int) ($rows[$bidang] ?? 0),
                'persen' => $totalPrestasi > 0 ? round(($rows[$bidang] ?? 0) / $totalPrestasi * 100) : 0,
                'warna'  => self::WARNA_BIDANG[$bidang],
            ])
            ->filter(fn ($item) => $item['jumlah'] > 0)
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | 6. KOMPOSISI JUARA (donut, agregat sistem, kategori dinamis, top 6)
    |--------------------------------------------------------------------------
    */
    private function komposisiJuara(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus,
        int $totalPrestasi
    ): Collection {
        $rows = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)
            ->groupBy('juara')
            ->selectRaw('juara, COUNT(*) as jumlah')
            ->get();

        return $rows
            ->map(fn ($row) => [
                'label'  => $row->juara ?: 'Tidak diketahui',
                'jumlah' => (int) $row->jumlah,
                'persen' => $totalPrestasi > 0 ? round($row->jumlah / $totalPrestasi * 100) : 0,
            ])
            ->sortByDesc('jumlah')
            ->values()
            ->take(6)
            ->map(function ($item, $index) {
                $item['warna'] = self::WARNA_JUARA[$index] ?? '#cbd5e1';
                return $item;
            });
    }

    /*
    |--------------------------------------------------------------------------
    | 7. SEBARAN PRESTASI — CROSS-TAB BIDANG x TINGKAT (agregat sistem)
    |--------------------------------------------------------------------------
    */
    private function sebaranTingkat(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus
    ): Collection {
        $rows = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)
            ->groupBy('bidang_prestasi', 'tingkat')
            ->selectRaw('bidang_prestasi as bidang, tingkat, COUNT(*) as jumlah')
            ->get();

        return collect(self::URUTAN_BIDANG)
            ->map(function ($bidang) use ($rows) {
                $perTingkat = collect(self::URUTAN_TINGKAT)->mapWithKeys(function ($tingkat) use ($rows, $bidang) {
                    $match = $rows->first(fn ($r) => $r->bidang === $bidang && $r->tingkat === $tingkat);

                    return [$tingkat => (int) ($match->jumlah ?? 0)];
                });

                return [
                    'bidang'      => $bidang,
                    'per_tingkat' => $perTingkat,
                    'total'       => $perTingkat->sum(),
                ];
            })
            ->filter(fn ($row) => $row['total'] > 0)
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | 8. INDIVIDU vs BEREGU (donut, agregat sistem)
    |--------------------------------------------------------------------------
    */
    private function komposisiKategori(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus,
        int $totalPrestasi
    ): Collection {
        $rows = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)
            ->groupBy('kategori_kegiatan')
            ->selectRaw('kategori_kegiatan as kategori, COUNT(*) as jumlah')
            ->get();

        return $rows->map(fn ($row) => [
            'label'  => $row->kategori ?: 'Tidak diketahui',
            'jumlah' => (int) $row->jumlah,
            'persen' => $totalPrestasi > 0 ? round($row->jumlah / $totalPrestasi * 100) : 0,
        ])->values();
    }

    /*
    |--------------------------------------------------------------------------
    | 9. LURING vs DARING (donut, agregat sistem)
    |--------------------------------------------------------------------------
    */
    private function komposisiMetode(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus,
        int $totalPrestasi
    ): Collection {
        $rows = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)
            ->groupBy('metode_pelaksanaan')
            ->selectRaw('metode_pelaksanaan as metode, COUNT(*) as jumlah')
            ->get();

        return $rows->map(fn ($row) => [
            'label'  => $row->metode ?: 'Tidak diketahui',
            'jumlah' => (int) $row->jumlah,
            'persen' => $totalPrestasi > 0 ? round($row->jumlah / $totalPrestasi * 100) : 0,
        ])->values();
    }

    /*
    |--------------------------------------------------------------------------
    | 10. DISTRIBUSI KEGIATAN PER BULAN (agregat sistem)
    |--------------------------------------------------------------------------
    */
    private function distribusiBulan(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus
    ): Collection {
        $rows = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)
            ->whereNotNull('waktu_kegiatan')
            ->groupBy(DB::raw('MONTH(waktu_kegiatan)'))
            ->selectRaw('MONTH(waktu_kegiatan) as bulan, COUNT(*) as jumlah')
            ->pluck('jumlah', 'bulan');

        return collect(range(1, 12))->map(fn ($bulanIndex) => [
            'label'  => self::NAMA_BULAN[$bulanIndex - 1],
            'jumlah' => (int) ($rows[$bulanIndex] ?? 0),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 11. TOP LEMBAGA PENYELENGGARA (agregat sistem)
    |--------------------------------------------------------------------------
    */
    private function topLembaga(
        int $periode,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus,
        int $totalPrestasi
    ): Collection {
        $rows = $this->basePrestasiQuery($periode, $jenjangFilter, $kotaFilter, $madrasahIdsStatus)
            ->groupBy('lembaga_penyelenggara')
            ->selectRaw('lembaga_penyelenggara as lembaga, COUNT(*) as jumlah')
            ->orderByDesc('jumlah')
            ->limit(self::TOP_LEMBAGA_DITAMPILKAN)
            ->get();

        return $rows->map(fn ($row) => [
            'lembaga' => $row->lembaga ?: 'Tidak diketahui',
            'jumlah'  => (int) $row->jumlah,
            'persen'  => $totalPrestasi > 0 ? round($row->jumlah / $totalPrestasi * 100) : 0,
        ]);
    }
}