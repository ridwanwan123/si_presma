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

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | BAGIAN 1 — RINGKASAN GLOBAL (untuk laporan ke Kabid/Kakanwil)
        |--------------------------------------------------------------------------
        | SELALU bisa dihitung walau baru ada 1 periode berjalan -- sumbernya
        | data prestasi MENTAH (prestasi_siswas), bukan arsip, jadi tidak perlu
        | nunggu proses assign-nilai-finalisasi-arsip selesai dulu.
        |--------------------------------------------------------------------------
        */
        $matrixTingkat = $this->hitungPerbandinganTingkat();
        $ringkasanPeriode = $this->hitungRingkasanPeriodeBerjalan();
        $persenPeningkatan = $this->hitungPersenPeningkatan($matrixTingkat);

        /*
        |--------------------------------------------------------------------------
        | BAGIAN 2 — PERKEMBANGAN MADRASAH (lintas tahun, butuh data arsip)
        |--------------------------------------------------------------------------
        */
        $daftarArsip = RankingArsip::orderBy('periode')->get();

        $trenSistem = collect();
        $rataJenjang = collect();
        $periodePembanding = null;
        $kenaikanTerbesar = collect();
        $penurunanTerbesar = collect();
        $daftarMadrasah = collect();
        $profilMadrasah = null;
        $madrasahIdFilter = $request->integer('madrasah_id') ?: null;

        if ($daftarArsip->isNotEmpty()) {

            $trenSistem = $this->hitungTrenSistem($daftarArsip);
            $rataJenjang = $this->hitungRataJenjang($daftarArsip);

            [$periodePembanding, $kenaikanTerbesar, $penurunanTerbesar] = $this->hitungPerubahan($daftarArsip);

            $daftarMadrasah = RankingArsipDetail::select('madrasah_id', 'nama_madrasah')
                ->whereNotNull('madrasah_id')
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
            'periodePembanding',
            'kenaikanTerbesar',
            'penurunanTerbesar',
            'daftarMadrasah',
            'madrasahIdFilter',
            'profilMadrasah',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT — satu method, beda "tipe" per komponen/card.
    |--------------------------------------------------------------------------
    */
    public function export(Request $request)
    {
        $tipe = $request->query('tipe');

        switch ($tipe) {

            case 'perbandingan-tingkat':
                $matrixTingkat = $this->hitungPerbandinganTingkat();

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
                $daftarArsip = RankingArsip::orderBy('periode')->get();

                $data = $this->hitungTrenSistem($daftarArsip)->map(fn ($row) => [
                    $row->periode,
                    $row->jumlah_madrasah,
                    $row->total_nilai,
                    $row->rata_rata,
                ]);

                $headings = ['Periode', 'Jumlah Madrasah', 'Total Nilai Sistem', 'Rata-rata Nilai'];
                $judul = 'Tren Sistem';
                break;

            case 'rata-jenjang':
                $daftarArsip = RankingArsip::orderBy('periode')->get();
                $rataJenjang = $this->hitungRataJenjang($daftarArsip);

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
                $daftarArsip = RankingArsip::orderBy('periode')->get();
                [$periodePembanding, $kenaikan, $penurunan] = $this->hitungPerubahan($daftarArsip);
                $dataset = $tipe === 'kenaikan' ? $kenaikan : $penurunan;

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

                $judul = $tipe === 'kenaikan' ? 'Kenaikan Terbesar' : 'Penurunan Terbesar';
                break;

            case 'profil-madrasah':
                $daftarArsip = RankingArsip::orderBy('periode')->get();
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
    | BAGIAN 1: PERBANDINGAN PRESTASI PER TINGKAT, TAHUN KE TAHUN
    |--------------------------------------------------------------------------
    | Dihitung dari data MENTAH prestasi_siswas (bukan arsip) -- jadi selalu
    | bisa tampil sejak periode pertama, tidak perlu tunggu proses penilaian
    | & arsip selesai. Ini angka partisipasi program, bukan hasil penilaian.
    |--------------------------------------------------------------------------
    */
    private function hitungPerbandinganTingkat(): array
    {
        $rows = PrestasiSiswa::visible()
            ->groupBy('periode', 'tingkat')
            ->selectRaw('periode, tingkat, COUNT(*) as jumlah')
            ->get();

        $periodeList = $rows->pluck('periode')->unique()->sort()->values();

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
    private function hitungRingkasanPeriodeBerjalan(): array
    {
        $periodeAktif = PeriodeAktif::aktif();

        $totalPrestasi = PrestasiSiswa::visible()
            ->where('periode', $periodeAktif)
            ->count();

        $madrasahAktif = PrestasiSiswa::visible()
            ->where('periode', $periodeAktif)
            ->distinct('madrasah_id')
            ->count('madrasah_id');

        $totalMadrasahTerdaftar = Madrasah::count();

        $madrasahFinished = PrestasiSiklus::where('periode', $periodeAktif)
            ->where('status', PrestasiSiklus::FINISHED)
            ->count();

        return [
            'periode_aktif'           => $periodeAktif,
            'total_prestasi'          => $totalPrestasi,
            'madrasah_aktif'          => $madrasahAktif,
            'total_madrasah_terdaftar' => $totalMadrasahTerdaftar,
            'madrasah_finished'       => $madrasahFinished,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN 1: PERSENTASE PENINGKATAN PRESTASI (per tingkat + total),
    | membandingkan 2 periode TERAKHIR yang datanya ada -- bukan cuma
    | periode aktif vs periode aktif - 1 (karena bisa saja loncat tahun).
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
    */
    private function hitungTrenSistem(Collection $daftarArsip): Collection
    {
        return $daftarArsip->map(function ($arsip) {
            $agg = RankingArsipDetail::where('ranking_arsip_id', $arsip->id)
                ->selectRaw('SUM(total_nilai_akhir) as total, AVG(total_nilai_akhir) as rata, COUNT(*) as jumlah')
                ->first();

            return (object) [
                'periode'         => $arsip->periode,
                'total_nilai'     => round($agg->total ?? 0, 2),
                'rata_rata'       => round($agg->rata ?? 0, 2),
                'jumlah_madrasah' => (int) ($agg->jumlah ?? 0),
            ];
        });
    }

    private function hitungRataJenjang(Collection $daftarArsip): Collection
    {
        $rows = RankingArsipDetail::whereIn('ranking_arsip_id', $daftarArsip->pluck('id'))
            ->join('ranking_arsips', 'ranking_arsips.id', '=', 'ranking_arsip_details.ranking_arsip_id')
            ->whereNotNull('ranking_arsip_details.jenjang_madrasah')
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

    private function hitungPerubahan(Collection $daftarArsip): array
    {
        if ($daftarArsip->count() < 2) {
            return [null, collect(), collect()];
        }

        $terakhir = $daftarArsip->last();
        $sebelumnya = $daftarArsip->slice(-2, 1)->first();

        $detailTerakhir = RankingArsipDetail::where('ranking_arsip_id', $terakhir->id)
            ->get()
            ->keyBy('madrasah_id');

        $detailSebelumnya = RankingArsipDetail::where('ranking_arsip_id', $sebelumnya->id)
            ->get()
            ->keyBy('madrasah_id');

        $perubahan = collect();

        foreach ($detailTerakhir as $madrasahId => $detail) {

            if (!$madrasahId || !$detailSebelumnya->has($madrasahId)) {
                continue;
            }

            $sebelum = $detailSebelumnya->get($madrasahId);
            $selisih = round($detail->total_nilai_akhir - $sebelum->total_nilai_akhir, 2);

            $perubahan->push((object) [
                'madrasah_id'          => $madrasahId,
                'nama_madrasah'        => $detail->nama_madrasah,
                'jenjang_madrasah'     => $detail->jenjang_madrasah,
                'nilai_sebelumnya'     => $sebelum->total_nilai_akhir,
                'nilai_sekarang'       => $detail->total_nilai_akhir,
                'selisih'              => $selisih,
                'peringkat_sebelumnya' => $sebelum->peringkat,
                'peringkat_sekarang'   => $detail->peringkat,
            ]);
        }

        return [
            [
                'sebelumnya' => $sebelumnya->periode,
                'sekarang'   => $terakhir->periode,
            ],
            $perubahan->sortByDesc('selisih')->take(10)->values(),
            $perubahan->sortBy('selisih')->take(10)->values(),
        ];
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

    private function hitungPeringkatBidang(int $rankingArsipId, ?string $jenjang, string $kolom, int $madrasahId): ?int
    {
        $urutan = RankingArsipDetail::where('ranking_arsip_id', $rankingArsipId)
            ->when($jenjang, fn ($q) => $q->where('jenjang_madrasah', $jenjang))
            ->orderByDesc($kolom)
            ->pluck('madrasah_id');

        $posisi = $urutan->search($madrasahId);

        return $posisi !== false ? $posisi + 1 : null;
    }
}