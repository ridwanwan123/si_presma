<?php

namespace App\Http\Controllers;

use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Models\PrestasiSiklus;
use App\Services\PenguranganPoinService;
use App\Exports\RankingLiveExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RankingController extends Controller
{
    private const URUTAN_BIDANG = [
        'Akademik',
        'Non Akademik',
        'Keagamaan',
        'GTK',
        'Lembaga',
    ];

    public function __construct(
        private PenguranganPoinService $penguranganPoinService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | HASIL & RANKING (SISI ADMINISTRATOR)
    |--------------------------------------------------------------------------
    | JMA menentukan juara PER BIDANG x PER JENJANG -- bukan satu papan
    | peringkat gabungan. Jadi halaman ini menampilkan 5 papan sekaligus
    | (satu per bidang), semuanya sudah terfilter ke jenjang yang dipilih.
    | "Total Keseluruhan" tetap dihitung sebagai referensi/statistik saja,
    | BUKAN penentu juara.
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | HASIL & RANKING (SISI ADMINISTRATOR)
    |--------------------------------------------------------------------------
    | JMA menentukan juara PER BIDANG x PER JENJANG -- bukan satu papan
    | peringkat gabungan. Jadi halaman ini menampilkan semua jenjang
    | sekaligus (dipisah per tab di tampilan web), masing-masing dengan
    | 5 papan bidangnya sendiri. "Total" tetap dihitung sebagai
    | referensi/statistik saja, BUKAN penentu juara.
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();
        $statusFilter = $request->query('status');
        $kotaFilter = $request->query('kota');

        $daftarPeriode = $this->daftarPeriodeFinished($periode);
        $daftarStatus = $this->daftarStatusFinished($periode);
        $daftarKota = $this->daftarKotaFinished($periode);
        $hasil = $this->hitungRankingPerBidang($periode, $statusFilter, $kotaFilter);

        $breadcrumb = breadcrumb([
            'Hasil & Ranking'
        ]);

        return view('ranking.index', compact(
            'hasil',
            'daftarStatus',
            'statusFilter',
            'daftarKota',
            'kotaFilter',
            'daftarPeriode',
            'periode',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL — satu file, beberapa SHEET (per jenjang x per bidang).
    | Mengikuti filter periode, status, & kota yang sedang aktif di halaman.
    |--------------------------------------------------------------------------
    */
    public function export(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();
        $statusFilter = $request->query('status');
        $kotaFilter = $request->query('kota');

        $hasil = $this->hitungRankingPerBidang($periode, $statusFilter, $kotaFilter);

        $namaFile = 'Ranking-Prestasi-Periode-' . $periode
            . ($statusFilter ? '-' . str_replace('/', '-', $statusFilter) : '')
            . ($kotaFilter ? '-' . str_replace('/', '-', $kotaFilter) : '')
            . '.xlsx';

        return Excel::download(
            new RankingLiveExport($hasil, $periode),
            $namaFile
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR PERIODE UNTUK DROPDOWN
    |--------------------------------------------------------------------------
    */
    private function daftarPeriodeFinished(int $periodeAktif)
    {
        $daftarPeriode = PrestasiSiklus::where('status', PrestasiSiklus::FINISHED)
            ->select('periode')
            ->distinct()
            ->pluck('periode');

        if (! $daftarPeriode->contains($periodeAktif)) {
            $daftarPeriode->push($periodeAktif);
        }

        return $daftarPeriode->sortDesc()->values();
    }

    private function madrasahIdsFinished(int $periode)
    {
        return PrestasiSiklus::where('periode', $periode)
            ->where('status', PrestasiSiklus::FINISHED)
            ->pluck('madrasah_id');
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR STATUS (NEGERI/SWASTA) & KOTA UNTUK DROPDOWN FILTER
    |--------------------------------------------------------------------------
    | NOTE: asumsi nama kolom "status_madrasah" (nilai: Negeri/Swasta).
    | Sesuaikan nama kolomnya kalau di model Madrasah berbeda.
    |--------------------------------------------------------------------------
    */
    private function daftarStatusFinished(int $periode)
    {
        return Madrasah::whereIn('id', $this->madrasahIdsFinished($periode))
            ->select('status_madrasah')
            ->distinct()
            ->orderBy('status_madrasah')
            ->pluck('status_madrasah');
    }

    private function daftarKotaFinished(int $periode)
    {
        return Madrasah::whereIn('id', $this->madrasahIdsFinished($periode))
            ->select('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');
    }

    /*
    |--------------------------------------------------------------------------
    | HITUNG RANKING PER BIDANG — dipakai bareng oleh index() dan export(),
    | supaya logicnya cuma ditulis SEKALI dan selalu konsisten.
    |--------------------------------------------------------------------------
    | Mengembalikan:
    | - 'per_jenjang' => data dikelompokkan PER JENJANG (RA/MI/MTs/MA, dst).
    |    Madrasah dari jenjang berbeda TIDAK PERNAH diadu dalam satu papan
    |    yang sama — setiap jenjang punya 5 papan bidangnya sendiri-sendiri
    |    (Akademik, Non Akademik, Keagamaan, GTK, Lembaga), masing-masing
    |    dengan peringkat 1..N miliknya sendiri.
    |
    |    Struktur: [jenjang => ['per_bidang' => [...], 'total' => [...]]]
    |
    |    - per_bidang: 5 papan bidang untuk jenjang itu, sudah diurutkan &
    |      diberi peringkat berdasarkan nilai_akhir bidang itu saja. Madrasah
    |      yang nilai mentahnya 0 di bidang itu TIDAK dimasukkan ke papan itu.
    |    - total: tabel referensi total gabungan semua bidang UNTUK JENJANG
    |      ITU SAJA — tetap BUKAN dasar penentuan juara.
    |--------------------------------------------------------------------------
    */
    private function hitungRankingPerBidang(
        int $periode,
        ?string $statusFilter = null,
        ?string $kotaFilter = null
    ): array {
        $madrasahIdsFinished = $this->madrasahIdsFinished($periode);

        /*
        |--------------------------------------------------------------------------
        | Nilai per (madrasah, bidang) -- satu query agregat untuk semua
        | madrasah & semua bidang sekaligus.
        |--------------------------------------------------------------------------
        */
        $rows = DB::table('penilaian_prestasis')
            ->join('prestasi_siswas', 'prestasi_siswas.id', '=', 'penilaian_prestasis.prestasi_siswa_id')
            ->where('penilaian_prestasis.status', 'completed')
            // Prestasi yang tidak diakui (ditandai Asesor saat menilai)
            // dikecualikan TOTAL dari sini -- seperti tidak pernah ada,
            // bukan cuma diberi nilai 0.
            ->where('prestasi_siswas.diakui', true)
            ->whereIn('prestasi_siswas.madrasah_id', $madrasahIdsFinished)
            ->where('prestasi_siswas.periode', $periode)
            ->groupBy('prestasi_siswas.madrasah_id', 'prestasi_siswas.bidang_prestasi')
            ->selectRaw('
                prestasi_siswas.madrasah_id,
                prestasi_siswas.bidang_prestasi,
                SUM(penilaian_prestasis.nilai_akhir) as total_nilai,
                COUNT(*) as jumlah
            ')
            ->get()
            ->groupBy('madrasah_id');

        $madrasahs = Madrasah::whereIn('id', $madrasahIdsFinished)
            // NOTE: asumsi nama kolom "status_madrasah" (Negeri/Swasta) --
            // sesuaikan kalau nama kolom aslinya berbeda.
            ->when($statusFilter, function ($q) use ($statusFilter) {
                $q->where('status_madrasah', $statusFilter);
            })
            ->when($kotaFilter, function ($q) use ($kotaFilter) {
                $q->where('kota', $kotaFilter);
            })
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Data lengkap per madrasah: nilai mentah + potongan + nilai akhir,
        | SEMUANYA sudah dipecah per bidang.
        |--------------------------------------------------------------------------
        */
        $dataLengkap = $madrasahs->map(function ($madrasah) use ($rows, $periode) {

            $barisBidang = $rows->get($madrasah->id, collect());

            $nilaiPerBidang = collect(self::URUTAN_BIDANG)->mapWithKeys(function ($bidang) use ($barisBidang) {
                $match = $barisBidang->first(fn ($r) => $r->bidang_prestasi === $bidang);

                return [$bidang => (float) ($match->total_nilai ?? 0)];
            })->toArray();

            $jumlahDinilai = $barisBidang->sum('jumlah');

            $hasilPotongan = $this->penguranganPoinService->hitungSetelahPotonganPerBidang(
                $madrasah->id,
                $periode,
                $nilaiPerBidang
            );

            return (object) [
                'madrasah_id'        => $madrasah->id,
                'nama_madrasah'      => $madrasah->nama_madrasah,
                'npsn'               => $madrasah->npsn,
                'jenjang_madrasah'   => $madrasah->jenjang_madrasah,
                'status_madrasah'    => $madrasah->status_madrasah,
                'kota'               => $madrasah->kota,
                'jumlah_dinilai'     => $jumlahDinilai,
                'per_bidang'         => $hasilPotongan['per_bidang'],
                'total_nilai_mentah' => $hasilPotongan['total_nilai_mentah'],
                'total_potongan'     => $hasilPotongan['total_potongan'],
                // Dibungkus int -- referensi saja, bukan penentu juara.
                'total_nilai_akhir'  => (int) round($hasilPotongan['total_nilai_akhir']),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | KELOMPOKKAN PER JENJANG -- setiap jenjang punya 5 papan bidangnya
        | sendiri, madrasah antar jenjang TIDAK PERNAH diadu dalam satu papan.
        |--------------------------------------------------------------------------
        */
        $rankingPerJenjang = $dataLengkap
            ->groupBy('jenjang_madrasah')
            ->sortKeys()
            ->map(function ($dataJenjang) {

                $rankingPerBidang = collect(self::URUTAN_BIDANG)->mapWithKeys(function ($bidang) use ($dataJenjang) {

                    $papan = $dataJenjang
                        ->map(function ($item) use ($bidang) {
                            $b = $item->per_bidang[$bidang];

                            return (object) [
                                'madrasah_id'            => $item->madrasah_id,
                                'nama_madrasah'          => $item->nama_madrasah,
                                'npsn'                   => $item->npsn,
                                'jenjang_madrasah'       => $item->jenjang_madrasah,
                                'status_madrasah'        => $item->status_madrasah,
                                'kota'                   => $item->kota,
                                'jumlah_dinilai'         => $item->jumlah_dinilai,
                                'nilai_mentah'           => $b['nilai_mentah'],
                                'potongan_aduan'         => $b['potongan_aduan'],
                                'potongan_keterlambatan' => $b['potongan_keterlambatan'],
                                'total_potongan'         => $b['total_potongan'],
                                // Dibungkus int supaya tidak muncul ".00".
                                'nilai_akhir'            => (int) round($b['nilai_akhir']),
                            ];
                        })
                        // Madrasah yang tidak punya prestasi sama sekali di
                        // bidang ini tidak usah muncul di papan bidang itu.
                        ->filter(fn ($row) => $row->nilai_mentah > 0)
                        ->sortByDesc('nilai_akhir')
                        ->values()
                        ->map(function ($row, $index) {
                            $row->peringkat = $index + 1;
                            return $row;
                        });

                    return [$bidang => $papan];
                });

                /*
                |----------------------------------------------------------------
                | TABEL TOTAL PER JENJANG -- referensi/statistik saja, BUKAN
                | dasar penentuan juara (juara ditentukan per bidang di atas).
                |----------------------------------------------------------------
                */
                $totalJenjang = $dataJenjang
                    ->sortByDesc('total_nilai_akhir')
                    ->values()
                    ->map(function ($item, $index) {
                        $item->peringkat = $index + 1;
                        return $item;
                    });

                return [
                    'per_bidang' => $rankingPerBidang,
                    'total'      => $totalJenjang,
                ];
            });

        return [
            'per_jenjang' => $rankingPerJenjang,
        ];
    }
}