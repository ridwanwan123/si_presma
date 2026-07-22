<?php

namespace App\Http\Controllers;

use App\Exports\DashboardExport;
use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Models\PrestasiSiklus;
use App\Models\PrestasiSiswa;
use App\Models\RankingArsip;
use App\Models\RankingArsipDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    private const BIDANG_KOLOM = [
        'Akademik'     => 'nilai_akademik',
        'Non Akademik' => 'nilai_non_akademik',
        'Keagamaan'    => 'nilai_keagamaan',
        'GTK'          => 'nilai_gtk',
        'Lembaga'      => 'nilai_lembaga',
    ];

    private const URUTAN_TINGKAT = [
        'Kabupaten/Kota',
        'Provinsi',
        'Nasional',
        'Internasional',
    ];

    /*
    |--------------------------------------------------------------------------
    | JUMLAH PERIODE TERAKHIR YANG DITAMPILKAN
    |--------------------------------------------------------------------------
    */
    private const JUMLAH_PERIODE_DITAMPILKAN = 5;

    /*
    |--------------------------------------------------------------------------
    | FILTER JENJANG
    |--------------------------------------------------------------------------
    | Default-nya sekarang "Semua Jenjang" (tidak ada filter jenjang aktif
    | sama sekali) begitu halaman pertama dibuka -- sesuai permintaan,
    | tidak lagi otomatis ke MI.
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        [$jenjangFilter, $statusFilter, $kotaFilter] = $this->bacaFilter($request);
        $opsiFilter = $this->opsiFilter();

        // Daftar id madrasah sesuai status (Negeri/Swasta) -- dihitung
        // SEKALI di sini, dipakai berulang di semua method di bawah,
        // supaya tidak query Madrasah berkali-kali untuk hal yang sama.
        $madrasahIdsStatus = $this->madrasahIdsByStatus($statusFilter);

        /*
        |--------------------------------------------------------------------------
        | BAGIAN 1 — RINGKASAN GLOBAL (untuk laporan ke Kabid/Kakanwil)
        |--------------------------------------------------------------------------
        */
        $matrixTingkat = $this->hitungPerbandinganTingkat($jenjangFilter, $kotaFilter, $madrasahIdsStatus);
        $ringkasanPeriode = $this->hitungRingkasanPeriodeBerjalan($jenjangFilter, $kotaFilter, $madrasahIdsStatus);
        $persenPeningkatan = $this->hitungPersenPeningkatan($matrixTingkat);

        /*
        |--------------------------------------------------------------------------
        | BAGIAN 2 — PERKEMBANGAN MADRASAH (lintas tahun, butuh data arsip)
        |--------------------------------------------------------------------------
        */
        $daftarArsip = $this->daftarArsipTerbatas();

        $trenSistem = ['agregat' => collect(), 'per_jenjang' => collect()];
        $rataJenjang = collect();
        $hasilPerubahan = ['periode' => null, 'per_bidang' => collect()];
        $daftarMadrasah = collect();
        $profilMadrasah = null;
        $madrasahIdFilter = $request->integer('madrasah_id') ?: null;

        if ($daftarArsip->isNotEmpty()) {

            $trenSistem = $this->hitungTrenSistem($daftarArsip, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);
            $rataJenjang = $this->hitungRataJenjang($daftarArsip, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);

            $hasilPerubahan = $this->hitungPerubahan(
                $daftarArsip,
                $jenjangFilter,
                $kotaFilter,
                $madrasahIdsStatus
            );

            $daftarMadrasah = RankingArsipDetail::select('madrasah_id', 'nama_madrasah')
                ->whereNotNull('madrasah_id')
                ->when($jenjangFilter, fn ($q) => $q->where('jenjang_madrasah', $jenjangFilter))
                ->when($kotaFilter, fn ($q) => $q->where('kota', $kotaFilter))
                ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('madrasah_id', $madrasahIdsStatus))
                ->distinct()
                ->orderBy('nama_madrasah')
                ->get();

            if ($madrasahIdFilter) {
                $profilMadrasah = $this->hitungProfilMadrasah($madrasahIdFilter, $daftarArsip);
            }
        }

        $breadcrumb = breadcrumb(['Dashboard']);

        return view('dashboard.index', compact(
            'matrixTingkat',
            'ringkasanPeriode',
            'persenPeningkatan',
            'daftarArsip',
            'trenSistem',
            'rataJenjang',
            'hasilPerubahan',
            'daftarMadrasah',
            'madrasahIdFilter',
            'profilMadrasah',
            'jenjangFilter',
            'statusFilter',
            'kotaFilter',
            'opsiFilter',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT — satu method, beda "tipe" per komponen/card. Filter yang
    | sedang aktif di halaman TETAP diikutkan (dikirim balik dari blade
    | lewat query string), supaya isi Excel konsisten sama yang di layar.
    |--------------------------------------------------------------------------
    */
    public function export(Request $request)
    {
        $tipe = $request->query('tipe');
        [$jenjangFilter, $statusFilter, $kotaFilter] = $this->bacaFilter($request);
        $madrasahIdsStatus = $this->madrasahIdsByStatus($statusFilter);

        switch ($tipe) {

            case 'perbandingan-tingkat':
                $matrixTingkat = $this->hitungPerbandinganTingkat($jenjangFilter, $kotaFilter, $madrasahIdsStatus);

                $data = $matrixTingkat['matrix']->map(function ($row) use ($matrixTingkat) {
                    $baris = [$row['tingkat']];

                    foreach ($matrixTingkat['periodeList'] as $periode) {
                        $baris[] = $row['per_tahun'][$periode];
                    }

                    $baris[] = $row['total'];

                    return $baris;
                });

                $barisTotal = ['TOTAL'];
                foreach ($matrixTingkat['periodeList'] as $periode) {
                    $barisTotal[] = $matrixTingkat['totalPerTahun'][$periode];
                }
                $barisTotal[] = $matrixTingkat['totalKeseluruhan'];
                $data->push($barisTotal);

                $headings = array_merge(
                    ['Tingkat'],
                    $matrixTingkat['periodeList']->map(fn ($p) => (string) $p)->toArray(),
                    ['Total']
                );

                $judul = 'Perbandingan per Tingkat';
                break;

            case 'tren-sistem':
                $daftarArsip = $this->daftarArsipTerbatas();
                $trenSistem = $this->hitungTrenSistem($daftarArsip, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);

                $data = $trenSistem['agregat']->map(function ($row) use ($trenSistem) {
                    $baris = [
                        $row->periode,
                        $row->jumlah_madrasah,
                        $row->total_nilai,
                        $row->rata_rata,
                    ];

                    foreach ($trenSistem['per_jenjang'] as $dataJenjang) {
                        $baris[] = $dataJenjang['per_tahun'][$row->periode] ?? 0;
                    }

                    return $baris;
                });

                $headings = array_merge(
                    ['Periode', 'Jumlah Madrasah', 'Total Nilai Sistem', 'Rata-rata Nilai'],
                    $trenSistem['per_jenjang']->map(fn ($row) => 'Total ' . $row['jenjang'])->toArray()
                );

                $judul = 'Tren Sistem';
                break;

            case 'rata-jenjang':
                $daftarArsip = $this->daftarArsipTerbatas();
                $rataJenjang = $this->hitungRataJenjang($daftarArsip, $jenjangFilter, $kotaFilter, $madrasahIdsStatus);

                $data = $rataJenjang->map(function ($row) {
                    $baris = [$row['jenjang']];

                    foreach ($row['per_tahun'] as $nilai) {
                        $baris[] = $nilai;
                    }

                    return $baris;
                });

                $headings = array_merge(
                    ['Jenjang'],
                    $daftarArsip->pluck('periode')->map(fn ($p) => (string) $p)->toArray()
                );

                $judul = 'Rata-rata per Jenjang';
                break;

            case 'kenaikan':
            case 'penurunan':
                $daftarArsip = $this->daftarArsipTerbatas();
                $hasilPerubahan = $this->hitungPerubahan(
                    $daftarArsip,
                    $jenjangFilter,
                    $kotaFilter,
                    $madrasahIdsStatus
                );

                $bidangDipilih = $request->query('bidang') ?: array_key_first(self::BIDANG_KOLOM);
                $kunciDataset = $tipe === 'kenaikan' ? 'kenaikan' : 'penurunan';
                $dataset = $hasilPerubahan['per_bidang'][$bidangDipilih][$kunciDataset] ?? collect();

                $data = $dataset->map(fn ($row) => [
                    $row->nama_madrasah,
                    $row->jenjang_madrasah,
                    $row->nilai_sebelumnya,
                    $row->nilai_sekarang,
                    $row->selisih,
                    $row->peringkat_sebelumnya,
                    $row->peringkat_sekarang,
                ]);

                $headings = [
                    'Nama Madrasah', 'Jenjang', 'Nilai Sebelumnya', 'Nilai Sekarang',
                    'Selisih', 'Peringkat Sebelumnya', 'Peringkat Sekarang',
                ];

                $judul = ($tipe === 'kenaikan' ? 'Kenaikan Terbesar' : 'Penurunan Terbesar') . ' - ' . $bidangDipilih;
                break;

            case 'profil-madrasah':
                $daftarArsip = $this->daftarArsipTerbatas();
                $madrasahId = $request->integer('madrasah_id');
                $profil = $this->hitungProfilMadrasah($madrasahId, $daftarArsip);

                if (!$profil) {
                    return back()->with('error', 'Data madrasah tidak ditemukan di arsip.');
                }

                $data = $profil->histori->map(fn ($row) => [
                    $row->periode,
                    $row->nilai_akademik, $row->peringkat_per_bidang['Akademik'],
                    $row->nilai_non_akademik, $row->peringkat_per_bidang['Non Akademik'],
                    $row->nilai_keagamaan, $row->peringkat_per_bidang['Keagamaan'],
                    $row->nilai_gtk, $row->peringkat_per_bidang['GTK'],
                    $row->nilai_lembaga, $row->peringkat_per_bidang['Lembaga'],
                    $row->total_nilai_asesor, $row->potongan_aduan, $row->potongan_keterlambatan,
                    $row->total_nilai_akhir, $row->peringkat_keseluruhan,
                ]);

                $headings = [
                    'Periode',
                    'Nilai Akademik', 'Peringkat Akademik',
                    'Nilai Non Akademik', 'Peringkat Non Akademik',
                    'Nilai Keagamaan', 'Peringkat Keagamaan',
                    'Nilai GTK', 'Peringkat GTK',
                    'Nilai Lembaga', 'Peringkat Lembaga',
                    'Total Nilai Asesor', 'Potongan Aduan', 'Potongan Keterlambatan',
                    'Total Nilai Akhir', 'Peringkat Keseluruhan',
                ];

                $judul = 'Profil ' . $profil->nama_madrasah;
                break;

            default:
                abort(404);
        }

        $namaFile = 'Dashboard-' . str_replace(' ', '-', $judul) . '.xlsx';

        return Excel::download(new DashboardExport(collect($data), $headings, $judul), $namaFile);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER: BACA DARI REQUEST
    |--------------------------------------------------------------------------
    | Jenjang default MI kalau parameter BENAR-BENAR belum pernah dikirim
    | (kunjungan pertama). Begitu user pilih "Semua Jenjang" (value=""),
    | itu tetap dihormati sebagai pilihan sadar, bukan balik ke default.
    | Status & Kota defaultnya "Semua" (string kosong).
    |--------------------------------------------------------------------------
    */
    private function bacaFilter(Request $request): array
    {
        $jenjangFilter = $request->query('jenjang', '');
        $statusFilter = $request->query('status', '');
        $kotaFilter = $request->query('kota', '');

        return [
            $jenjangFilter ?: null,
            $statusFilter ?: null,
            $kotaFilter ?: null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | OPSI DROPDOWN FILTER (jenjang, status, kota)
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | DAFTAR ID MADRASAH SESUAI STATUS (Negeri/Swasta)
    |--------------------------------------------------------------------------
    | null = filter status tidak aktif (semua status, tidak perlu dibatasi).
    | Dihitung SEKALI per request, dipakai berulang oleh semua method lain
    | via whereIn('madrasah_id', ...) -- baik untuk query prestasi_siswas
    | (lewat whereHas madrasah) maupun ranking_arsip_details (yang tidak
    | menyimpan status_madrasah sendiri, jadi HARUS lewat join/whereIn ini).
    |--------------------------------------------------------------------------
    */
    private function madrasahIdsByStatus(?string $statusFilter): ?Collection
    {
        if (!$statusFilter) {
            return null;
        }

        return Madrasah::where('status_madrasah', $statusFilter)->pluck('id');
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 1: PERBANDINGAN PRESTASI PER TINGKAT, TAHUN KE TAHUN
    |--------------------------------------------------------------------------
    */
    private function daftarArsipTerbatas(): Collection
    {
        return RankingArsip::orderByDesc('periode')
            ->limit(self::JUMLAH_PERIODE_DITAMPILKAN)
            ->get()
            ->sortBy('periode')
            ->values();
    }

    private function hitungPerbandinganTingkat(?string $jenjangFilter, ?string $kotaFilter, ?Collection $madrasahIdsStatus): array
    {
        $filterMadrasah = function ($query) use ($jenjangFilter, $kotaFilter, $madrasahIdsStatus) {
            $query->when($jenjangFilter, fn ($q) => $q->where('jenjang_madrasah', $jenjangFilter))
                ->when($kotaFilter, fn ($q) => $q->where('kota', $kotaFilter))
                ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('id', $madrasahIdsStatus));
        };

        $periodeList = PrestasiSiswa::visible()
            ->whereHas('madrasah', $filterMadrasah)
            ->select('periode')
            ->distinct()
            ->orderByDesc('periode')
            ->limit(self::JUMLAH_PERIODE_DITAMPILKAN)
            ->pluck('periode')
            ->sort()
            ->values();

        $rows = PrestasiSiswa::visible()
            ->whereHas('madrasah', $filterMadrasah)
            ->whereIn('periode', $periodeList)
            ->groupBy('periode', 'tingkat')
            ->selectRaw('periode, tingkat, COUNT(*) as jumlah')
            ->get();

        $matrix = collect(self::URUTAN_TINGKAT)->map(function ($tingkat) use ($rows, $periodeList) {
            $perTahun = $periodeList->mapWithKeys(function ($periode) use ($rows, $tingkat) {
                $match = $rows->first(fn ($r) => $r->periode == $periode && $r->tingkat === $tingkat);

                return [$periode => (int) ($match->jumlah ?? 0)];
            });

            return [
                'tingkat'   => $tingkat,
                'per_tahun' => $perTahun,
                'total'     => $perTahun->sum(),
            ];
        });

        $totalPerTahun = $periodeList->mapWithKeys(function ($periode) use ($matrix) {
            return [$periode => $matrix->sum(fn ($row) => $row['per_tahun'][$periode])];
        });

        return [
            'periodeList'      => $periodeList,
            'matrix'           => $matrix,
            'totalPerTahun'    => $totalPerTahun,
            'totalKeseluruhan' => $totalPerTahun->sum(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 1: RINGKASAN PERIODE YANG SEDANG BERJALAN
    |--------------------------------------------------------------------------
    */
    private function hitungRingkasanPeriodeBerjalan(?string $jenjangFilter, ?string $kotaFilter, ?Collection $madrasahIdsStatus): array
    {
        $periodeAktif = PeriodeAktif::aktif();

        $filterMadrasah = function ($query) use ($jenjangFilter, $kotaFilter, $madrasahIdsStatus) {
            $query->when($jenjangFilter, fn ($q) => $q->where('jenjang_madrasah', $jenjangFilter))
                ->when($kotaFilter, fn ($q) => $q->where('kota', $kotaFilter))
                ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('id', $madrasahIdsStatus));
        };

        $totalPrestasi = PrestasiSiswa::visible()
            ->whereHas('madrasah', $filterMadrasah)
            ->where('periode', $periodeAktif)
            ->count();

        $madrasahAktif = PrestasiSiswa::visible()
            ->whereHas('madrasah', $filterMadrasah)
            ->where('periode', $periodeAktif)
            ->distinct('madrasah_id')
            ->count('madrasah_id');

        $totalMadrasahTerdaftar = Madrasah::query()
            ->when($jenjangFilter, fn ($q) => $q->where('jenjang_madrasah', $jenjangFilter))
            ->when($kotaFilter, fn ($q) => $q->where('kota', $kotaFilter))
            ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('id', $madrasahIdsStatus))
            ->count();

        $madrasahFinished = PrestasiSiklus::whereHas('madrasah', $filterMadrasah)
            ->where('periode', $periodeAktif)
            ->where('status', PrestasiSiklus::FINISHED)
            ->count();

        return [
            'periode_aktif'            => $periodeAktif,
            'total_prestasi'           => $totalPrestasi,
            'madrasah_aktif'           => $madrasahAktif,
            'total_madrasah_terdaftar' => $totalMadrasahTerdaftar,
            'madrasah_finished'        => $madrasahFinished,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 1: PERSENTASE PENINGKATAN PRESTASI (per tingkat + total)
    |--------------------------------------------------------------------------
    */
    private function hitungPersenPeningkatan(array $matrixTingkat): ?array
    {
        $periodeList = $matrixTingkat['periodeList'];

        if ($periodeList->count() < 2) {
            return null;
        }

        $periodeSekarang = $periodeList->last();
        $periodeSebelumnya = $periodeList->slice(-2, 1)->first();

        $perTingkat = $matrixTingkat['matrix']->map(function ($row) use ($periodeSekarang, $periodeSebelumnya) {
            $sekarang = $row['per_tahun'][$periodeSekarang] ?? 0;
            $sebelumnya = $row['per_tahun'][$periodeSebelumnya] ?? 0;

            $persen = $sebelumnya > 0
                ? round((($sekarang - $sebelumnya) / $sebelumnya) * 100, 1)
                : ($sekarang > 0 ? 100.0 : 0.0);

            return [
                'tingkat' => $row['tingkat'],
                'persen'  => $persen,
            ];
        });

        $totalSekarang = $matrixTingkat['totalPerTahun'][$periodeSekarang] ?? 0;
        $totalSebelumnya = $matrixTingkat['totalPerTahun'][$periodeSebelumnya] ?? 0;

        $persenTotal = $totalSebelumnya > 0
            ? round((($totalSekarang - $totalSebelumnya) / $totalSebelumnya) * 100, 1)
            : ($totalSekarang > 0 ? 100.0 : 0.0);

        return [
            'periode_sekarang'   => $periodeSekarang,
            'periode_sebelumnya' => $periodeSebelumnya,
            'per_tingkat'        => $perTingkat,
            'persen_total'       => $persenTotal,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 2: TREN TOTAL PRESTASI SISTEM (dari arsip)
    |--------------------------------------------------------------------------
    | jenjang_madrasah & kota tersimpan LANGSUNG di ranking_arsip_details
    | (snapshot beku), jadi bisa difilter langsung tanpa join. status_madrasah
    | TIDAK tersimpan di sana, jadi filter status HARUS lewat whereIn
    | madrasah_id ke tabel Madrasah live -- baris arsip manual yang
    | madrasah_id-nya kosong otomatis tidak ikut kalau filter status aktif
    | (tidak ada cara mengetahui status Negeri/Swasta-nya).
    |--------------------------------------------------------------------------
    */
    private function hitungTrenSistem(Collection $daftarArsip, ?string $jenjangFilter, ?string $kotaFilter, ?Collection $madrasahIdsStatus): array
    {
        // Agregat keseluruhan per periode (tetap dipertahankan -- dipakai
        // juga oleh export sebagai kolom ringkasan).
        $agregat = $daftarArsip->map(function ($arsip) use ($jenjangFilter, $kotaFilter, $madrasahIdsStatus) {
            $agg = RankingArsipDetail::where('ranking_arsip_id', $arsip->id)
                ->when($jenjangFilter, fn ($q) => $q->where('jenjang_madrasah', $jenjangFilter))
                ->when($kotaFilter, fn ($q) => $q->where('kota', $kotaFilter))
                ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('madrasah_id', $madrasahIdsStatus))
                ->selectRaw('SUM(total_nilai_akhir) as total, AVG(total_nilai_akhir) as rata, COUNT(*) as jumlah')
                ->first();

            return (object) [
                'periode'         => $arsip->periode,
                'total_nilai'     => round($agg->total ?? 0, 2),
                'rata_rata'       => round($agg->rata ?? 0, 2),
                'jumlah_madrasah' => (int) ($agg->jumlah ?? 0),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | BARU: BREAKDOWN TOTAL PER JENJANG, PER PERIODE
        |--------------------------------------------------------------------------
        | Struktur hasilnya SENGAJA sama persis dengan hitungRataJenjang()
        | (array of ['jenjang' => .., 'per_tahun' => [...]]) supaya blade
        | bisa membangun chart multi-garis dengan cara yang sama, cuma beda
        | agregasinya: SUM (total) di sini, AVG (rata-rata) di sana.
        |--------------------------------------------------------------------------
        */
        $rows = RankingArsipDetail::whereIn('ranking_arsip_id', $daftarArsip->pluck('id'))
            ->join('ranking_arsips', 'ranking_arsips.id', '=', 'ranking_arsip_details.ranking_arsip_id')
            ->whereNotNull('ranking_arsip_details.jenjang_madrasah')
            ->when($jenjangFilter, fn ($q) => $q->where('ranking_arsip_details.jenjang_madrasah', $jenjangFilter))
            ->when($kotaFilter, fn ($q) => $q->where('ranking_arsip_details.kota', $kotaFilter))
            ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('ranking_arsip_details.madrasah_id', $madrasahIdsStatus))
            ->groupBy('ranking_arsips.periode', 'ranking_arsip_details.jenjang_madrasah')
            ->selectRaw('
                ranking_arsips.periode,
                ranking_arsip_details.jenjang_madrasah,
                SUM(ranking_arsip_details.total_nilai_akhir) as total
            ')
            ->get();

        $jenjangList = $rows->pluck('jenjang_madrasah')->unique()->sort()->values();

        $perJenjang = $jenjangList->map(function ($jenjang) use ($rows, $daftarArsip) {
            $perTahun = $daftarArsip->mapWithKeys(function ($arsip) use ($rows, $jenjang) {
                $match = $rows->first(fn ($r) => $r->periode == $arsip->periode && $r->jenjang_madrasah === $jenjang);

                return [$arsip->periode => round($match->total ?? 0, 2)];
            });

            return [
                'jenjang'   => $jenjang,
                'per_tahun' => $perTahun,
            ];
        });

        return [
            'agregat'     => $agregat,
            'per_jenjang' => $perJenjang,
        ];
    }

    private function hitungRataJenjang(Collection $daftarArsip, ?string $jenjangFilter, ?string $kotaFilter, ?Collection $madrasahIdsStatus): Collection
    {
        $rows = RankingArsipDetail::whereIn('ranking_arsip_id', $daftarArsip->pluck('id'))
            ->join('ranking_arsips', 'ranking_arsips.id', '=', 'ranking_arsip_details.ranking_arsip_id')
            ->whereNotNull('ranking_arsip_details.jenjang_madrasah')
            ->when($jenjangFilter, fn ($q) => $q->where('ranking_arsip_details.jenjang_madrasah', $jenjangFilter))
            ->when($kotaFilter, fn ($q) => $q->where('ranking_arsip_details.kota', $kotaFilter))
            ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('ranking_arsip_details.madrasah_id', $madrasahIdsStatus))
            ->groupBy('ranking_arsips.periode', 'ranking_arsip_details.jenjang_madrasah')
            ->selectRaw('
                ranking_arsips.periode,
                ranking_arsip_details.jenjang_madrasah,
                AVG(ranking_arsip_details.total_nilai_akhir) as rata
            ')
            ->get();

        $jenjangList = $rows->pluck('jenjang_madrasah')->unique()->sort()->values();

        return $jenjangList->map(function ($jenjang) use ($rows, $daftarArsip) {
            $perTahun = $daftarArsip->mapWithKeys(function ($arsip) use ($rows, $jenjang) {
                $match = $rows->first(fn ($r) => $r->periode == $arsip->periode && $r->jenjang_madrasah === $jenjang);

                return [$arsip->periode => round($match->rata ?? 0, 2)];
            });

            return [
                'jenjang'   => $jenjang,
                'per_tahun' => $perTahun,
            ];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | KENAIKAN/PENURUNAN -- DIROMBAK jadi PER BIDANG (bukan cuma total
    | gabungan seperti sebelumnya). Peringkat yang ditampilkan juga
    | direkonstruksi PER BIDANG (pakai aturan sama seperti
    | hitungPeringkatBidang()/Ranking Live: Keterlambatan dibagi rata 5
    | bidang, Aduan cuma menyunat Lembaga) -- bukan kolom 'peringkat'
    | gabungan yang tersimpan di RankingArsipDetail.
    |
    | Filter Jenjang yang sudah ada (di halaman) otomatis ikut berlaku di
    | sini juga -- kalau "Semua Jenjang" dipilih, kolom Jenjang tetap
    | ditampilkan di tabel supaya tetap kelihatan asalnya dari jenjang mana.
    |--------------------------------------------------------------------------
    */
    private function hitungPerubahan(Collection $daftarArsip, ?string $jenjangFilter, ?string $kotaFilter, ?Collection $madrasahIdsStatus): array
    {
        if ($daftarArsip->count() < 2) {
            return ['periode' => null, 'per_bidang' => collect()];
        }

        $terakhir = $daftarArsip->last();
        $sebelumnya = $daftarArsip->slice(-2, 1)->first();

        $perBidang = collect(self::BIDANG_KOLOM)->map(function ($kolom, $labelBidang) use ($terakhir, $sebelumnya, $jenjangFilter, $kotaFilter, $madrasahIdsStatus) {

            $papanTerakhir = $this->papanBidangUntukPeriode($terakhir->id, $jenjangFilter, $kotaFilter, $madrasahIdsStatus, $kolom, $labelBidang);
            $papanSebelumnya = $this->papanBidangUntukPeriode($sebelumnya->id, $jenjangFilter, $kotaFilter, $madrasahIdsStatus, $kolom, $labelBidang);

            $perubahan = collect();

            foreach ($papanTerakhir as $madrasahId => $baris) {

                if (!$madrasahId || !$papanSebelumnya->has($madrasahId)) {
                    continue;
                }

                $sebelum = $papanSebelumnya->get($madrasahId);
                $selisih = round($baris['nilai'] - $sebelum['nilai'], 2);

                $perubahan->push((object) [
                    'madrasah_id'          => $madrasahId,
                    'nama_madrasah'        => $baris['nama_madrasah'],
                    'jenjang_madrasah'     => $baris['jenjang_madrasah'],
                    'nilai_sebelumnya'     => $sebelum['nilai'],
                    'nilai_sekarang'       => $baris['nilai'],
                    'selisih'              => $selisih,
                    'peringkat_sebelumnya' => $sebelum['peringkat'],
                    'peringkat_sekarang'   => $baris['peringkat'],
                ]);
            }

            return [
                'kenaikan'   => $perubahan->sortByDesc('selisih')->take(10)->values(),
                'penurunan'  => $perubahan->sortBy('selisih')->take(10)->values(),
            ];
        });

        return [
            'periode' => [
                'sebelumnya' => $sebelumnya->periode,
                'sekarang'   => $terakhir->periode,
            ],
            'per_bidang' => $perBidang,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PAPAN 1 BIDANG UNTUK 1 ARSIP -- versi "bulk" dari hitungPeringkatBidang()
    | (yang aslinya cuma hitung 1 madrasah). Di sini SEMUA madrasah dihitung
    | sekaligus dalam 1 query + 1 sorting, supaya efisien dipanggil untuk
    | daftar kenaikan/penurunan (yang butuh peringkat SEMUA madrasah, bukan
    | cuma 1).
    |--------------------------------------------------------------------------
    */
    private function papanBidangUntukPeriode(
        int $rankingArsipId,
        ?string $jenjangFilter,
        ?string $kotaFilter,
        ?Collection $madrasahIdsStatus,
        string $kolom,
        string $labelBidang
    ): Collection {
        $baris = RankingArsipDetail::where('ranking_arsip_id', $rankingArsipId)
            ->when($jenjangFilter, fn ($q) => $q->where('jenjang_madrasah', $jenjangFilter))
            ->when($kotaFilter, fn ($q) => $q->where('kota', $kotaFilter))
            ->when($madrasahIdsStatus !== null, fn ($q) => $q->whereIn('madrasah_id', $madrasahIdsStatus))
            ->get(['madrasah_id', 'nama_madrasah', 'jenjang_madrasah', $kolom, 'potongan_aduan', 'potongan_keterlambatan']);

        return $baris
            ->map(function ($row) use ($kolom, $labelBidang) {
                $potonganKeterlambatanBidang = round($row->potongan_keterlambatan / 5, 2);
                $potonganAduanBidang = $labelBidang === 'Lembaga' ? $row->potongan_aduan : 0;
                $nilaiAkhirBidang = max(0, $row->$kolom - $potonganKeterlambatanBidang - $potonganAduanBidang);

                return [
                    'madrasah_id'      => $row->madrasah_id,
                    'nama_madrasah'    => $row->nama_madrasah,
                    'jenjang_madrasah' => $row->jenjang_madrasah,
                    'nilai'            => $nilaiAkhirBidang,
                ];
            })
            // Madrasah yang nilainya 0 di bidang ini dianggap tidak
            // berpartisipasi -- konsisten sama Ranking Live yang juga
            // mengecualikan madrasah begini dari papan bidang tsb.
            ->filter(fn ($r) => $r['nilai'] > 0)
            ->sortByDesc('nilai')
            ->values()
            ->map(function ($r, $i) {
                $r['peringkat'] = $i + 1;
                return $r;
            })
            ->keyBy('madrasah_id');
    }

    private function hitungProfilMadrasah(int $madrasahId, Collection $daftarArsip): ?object
    {
        $riwayat = RankingArsipDetail::where('madrasah_id', $madrasahId)
            ->whereIn('ranking_arsip_id', $daftarArsip->pluck('id'))
            ->get()
            ->keyBy('ranking_arsip_id');

        if ($riwayat->isEmpty()) {
            return null;
        }

        $namaMadrasah = $riwayat->first()->nama_madrasah;

        $histori = collect();

        foreach ($daftarArsip as $arsip) {

            $detail = $riwayat->get($arsip->id);

            if (!$detail) {
                continue;
            }

            $peringkatPerBidang = [];

            foreach (self::BIDANG_KOLOM as $labelBidang => $kolom) {
                $peringkatPerBidang[$labelBidang] = $this->hitungPeringkatBidang(
                    $arsip->id,
                    $detail->jenjang_madrasah,
                    $kolom,
                    $labelBidang,
                    $madrasahId
                );
            }

            $histori->push((object) [
                'periode'                => $arsip->periode,
                'peringkat_keseluruhan'  => $detail->peringkat,
                'nilai_akademik'         => $detail->nilai_akademik,
                'nilai_non_akademik'     => $detail->nilai_non_akademik,
                'nilai_keagamaan'        => $detail->nilai_keagamaan,
                'nilai_gtk'              => $detail->nilai_gtk,
                'nilai_lembaga'          => $detail->nilai_lembaga,
                'total_nilai_asesor'     => $detail->total_nilai_asesor,
                'potongan_aduan'         => $detail->potongan_aduan,
                'potongan_keterlambatan' => $detail->potongan_keterlambatan,
                'total_nilai_akhir'      => $detail->total_nilai_akhir,
                'peringkat_per_bidang'   => $peringkatPerBidang,
            ]);
        }

        return (object) [
            'madrasah_id'   => $madrasahId,
            'nama_madrasah' => $namaMadrasah,
            'histori'       => $histori,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PERINGKAT PER BIDANG — direkonstruksi dari nilai SETELAH potongan.
    |--------------------------------------------------------------------------
    */
    private function hitungPeringkatBidang(int $rankingArsipId, ?string $jenjang, string $kolom, string $labelBidang, int $madrasahId): ?int
    {
        $baris = RankingArsipDetail::where('ranking_arsip_id', $rankingArsipId)
            ->when($jenjang, fn ($q) => $q->where('jenjang_madrasah', $jenjang))
            ->get(['madrasah_id', $kolom, 'potongan_aduan', 'potongan_keterlambatan']);

        $urutan = $baris
            ->map(function ($row) use ($kolom, $labelBidang) {
                $potonganKeterlambatanBidang = round($row->potongan_keterlambatan / 5, 2);
                $potonganAduanBidang = $labelBidang === 'Lembaga' ? $row->potongan_aduan : 0;

                $nilaiAkhirBidang = max(0, $row->$kolom - $potonganKeterlambatanBidang - $potonganAduanBidang);

                return [
                    'madrasah_id'        => $row->madrasah_id,
                    'nilai_akhir_bidang' => $nilaiAkhirBidang,
                ];
            })
            ->sortByDesc('nilai_akhir_bidang')
            ->values()
            ->pluck('madrasah_id');

        $posisi = $urutan->search($madrasahId);

        return $posisi !== false ? $posisi + 1 : null;
    }
}