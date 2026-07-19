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
    public function index(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();
        $jenjangFilter = $request->query('jenjang');

        $daftarPeriode = $this->daftarPeriodeFinished($periode);
        $daftarJenjang = $this->daftarJenjangFinished($periode);
        $hasil = $this->hitungRankingPerBidang($periode, $jenjangFilter);

        $breadcrumb = breadcrumb([
            'Hasil & Ranking'
        ]);

        return view('ranking.index', compact(
            'hasil',
            'daftarJenjang',
            'jenjangFilter',
            'daftarPeriode',
            'periode',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL — satu file, beberapa SHEET (satu sheet per bidang +
    | satu sheet Total Keseluruhan). Mengikuti filter periode & jenjang
    | yang sedang aktif di halaman.
    |--------------------------------------------------------------------------
    */
    public function export(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();
        $jenjangFilter = $request->query('jenjang');

        $hasil = $this->hitungRankingPerBidang($periode, $jenjangFilter);

        $namaFile = 'Ranking-Prestasi-Periode-' . $periode
            . ($jenjangFilter ? '-' . str_replace('/', '-', $jenjangFilter) : '')
            . '.xlsx';

        return Excel::download(
            new RankingLiveExport($hasil, $periode, $jenjangFilter),
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

    private function daftarJenjangFinished(int $periode)
    {
        return Madrasah::whereIn('id', $this->madrasahIdsFinished($periode))
            ->select('jenjang_madrasah')
            ->distinct()
            ->orderBy('jenjang_madrasah')
            ->pluck('jenjang_madrasah');
    }

    /*
    |--------------------------------------------------------------------------
    | HITUNG RANKING PER BIDANG — dipakai bareng oleh index() dan export(),
    | supaya logicnya cuma ditulis SEKALI dan selalu konsisten.
    |--------------------------------------------------------------------------
    | Mengembalikan:
    | - 'per_bidang' => 5 papan terpisah (Akademik, Non Akademik, dst),
    |    masing-masing sudah diurutkan & diberi peringkat SENDIRI
    |    berdasarkan nilai_akhir bidang itu saja (bukan total gabungan).
    |    Madrasah yang nilai mentahnya 0 di bidang itu TIDAK dimasukkan ke
    |    papan itu (tidak ada yang mau di-ranking kalau memang tidak ikut).
    | - 'total' => tabel referensi total gabungan semua bidang, BUKAN
    |    dasar penentuan juara.
    |--------------------------------------------------------------------------
    */
    private function hitungRankingPerBidang(int $periode, ?string $jenjangFilter): array
    {
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
            ->when($jenjangFilter, function ($q) use ($jenjangFilter) {
                $q->where('jenjang_madrasah', $jenjangFilter);
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
                'kota'               => $madrasah->kota,
                'jumlah_dinilai'     => $jumlahDinilai,
                'per_bidang'         => $hasilPotongan['per_bidang'],
                'total_nilai_mentah' => $hasilPotongan['total_nilai_mentah'],
                'total_potongan'     => $hasilPotongan['total_potongan'],
                'total_nilai_akhir'  => $hasilPotongan['total_nilai_akhir'],
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | 5 PAPAN TERPISAH -- diurutkan & diberi peringkat MASING-MASING
        | berdasarkan nilai_akhir bidang itu sendiri.
        |--------------------------------------------------------------------------
        */
        $rankingPerBidang = collect(self::URUTAN_BIDANG)->mapWithKeys(function ($bidang) use ($dataLengkap) {

            $papan = $dataLengkap
                ->map(function ($item) use ($bidang) {
                    $b = $item->per_bidang[$bidang];

                    return (object) [
                        'madrasah_id'            => $item->madrasah_id,
                        'nama_madrasah'          => $item->nama_madrasah,
                        'npsn'                   => $item->npsn,
                        'jenjang_madrasah'       => $item->jenjang_madrasah,
                        'kota'                   => $item->kota,
                        'jumlah_dinilai'         => $item->jumlah_dinilai,
                        'nilai_mentah'           => $b['nilai_mentah'],
                        'potongan_aduan'         => $b['potongan_aduan'],
                        'potongan_keterlambatan' => $b['potongan_keterlambatan'],
                        'total_potongan'         => $b['total_potongan'],
                        'nilai_akhir'            => $b['nilai_akhir'],
                    ];
                })
                // Madrasah yang tidak punya prestasi sama sekali di bidang
                // ini tidak usah muncul di papan bidang itu.
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
        |--------------------------------------------------------------------------
        | TABEL TOTAL KESELURUHAN -- referensi/statistik saja, BUKAN dasar
        | penentuan juara (juara ditentukan per bidang di atas).
        |--------------------------------------------------------------------------
        */
        $rankingTotal = $dataLengkap
            ->sortByDesc('total_nilai_akhir')
            ->values()
            ->map(function ($item, $index) {
                $item->peringkat = $index + 1;
                return $item;
            });

        return [
            'per_bidang' => $rankingPerBidang,
            'total'      => $rankingTotal,
        ];
    }
}