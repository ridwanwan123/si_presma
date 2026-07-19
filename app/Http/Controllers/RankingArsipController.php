<?php

namespace App\Http\Controllers;

use App\Exports\RankingArsipExport;
use App\Helpers\ActivityLogger;
use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Models\PrestasiSiklus;
use App\Models\RankingArsip;
use App\Models\RankingArsipDetail;
use App\Services\PenguranganPoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RankingArsipController extends Controller
{
    private const PETA_KOLOM_BIDANG = [
        'Akademik'     => 'nilai_akademik',
        'Non Akademik' => 'nilai_non_akademik',
        'Keagamaan'    => 'nilai_keagamaan',
        'GTK'          => 'nilai_gtk',
        'Lembaga'      => 'nilai_lembaga',
    ];

    public function __construct(
        private PenguranganPoinService $penguranganPoinService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR ARSIP
    |--------------------------------------------------------------------------
    | Filter Jenjang di sini TIDAK menyembunyikan baris arsip (1 arsip tetap
    | 1 baris = 1 periode, snapshot selalu lengkap semua jenjang) -- filter
    | ini cuma mempersempit angka "Jumlah Madrasah" yang ditampilkan per
    | baris, supaya bisa lihat "berapa madrasah MI di arsip 2025" tanpa
    | perlu buka Kelola satu-satu.
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $jenjangFilter = $request->query('jenjang');

        $daftarArsip = RankingArsip::withCount(['details' => function ($query) use ($jenjangFilter) {
                $query->when($jenjangFilter, fn ($q) => $q->where('jenjang_madrasah', $jenjangFilter));
            }])
            ->with('diarsipkanOleh')
            ->orderByDesc('periode')
            ->get();

        $daftarJenjang = RankingArsipDetail::whereNotNull('jenjang_madrasah')
            ->distinct()
            ->orderBy('jenjang_madrasah')
            ->pluck('jenjang_madrasah');

        $breadcrumb = breadcrumb([
            'Hasil & Ranking' => route('ranking.index'),
            'Arsip Ranking'
        ]);

        return view('ranking-arsip.index', compact('daftarArsip', 'daftarJenjang', 'jenjangFilter', 'breadcrumb'));
    }

    /*
    |--------------------------------------------------------------------------
    | ARSIPKAN (MANUAL — dipicu tombol, BUKAN otomatis)
    |--------------------------------------------------------------------------
    | Selalu mengambil data GABUNGAN SEMUA JENJANG untuk periode terkait,
    | tidak terpengaruh filter jenjang yang mungkin sedang aktif di halaman
    | Ranking -- supaya arsip selalu lengkap, tidak pernah cuma sebagian.
    |
    | Kalau periode yang sama sudah pernah diarsipkan sebelumnya, data lama
    | akan DIGANTI (bukan menumpuk jadi 2 arsip untuk periode yang sama).
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode' => ['required', 'integer'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $periode = $validated['periode'];

        $dataRanking = $this->hitungDataLengkap($periode);

        if ($dataRanking->isEmpty()) {
            return redirect()
                ->route('ranking.index', ['periode' => $periode])
                ->with('error', 'Tidak ada madrasah FINISHED untuk diarsipkan pada periode ' . $periode . '.');
        }

        $arsip = DB::transaction(function () use ($periode, $dataRanking, $validated) {

            $arsip = RankingArsip::updateOrCreate(
                ['periode' => $periode],
                [
                    'diarsipkan_oleh' => auth()->id(),
                    'diarsipkan_pada' => now(),
                    'catatan' => $validated['catatan'] ?? null,
                ]
            );

            // Kalau ini re-arsip (perbarui), buang detail lama dulu supaya
            // tidak ada sisa baris madrasah yang sudah tidak relevan lagi.
            $arsip->details()->delete();

            foreach ($dataRanking as $item) {
                RankingArsipDetail::create([
                    'ranking_arsip_id'        => $arsip->id,
                    'madrasah_id'             => $item->madrasah_id,
                    'nama_madrasah'           => $item->nama_madrasah,
                    'npsn'                    => $item->npsn,
                    'jenjang_madrasah'        => $item->jenjang_madrasah,
                    'kota'                    => $item->kota,
                    'peringkat'               => $item->peringkat,
                    'nilai_akademik'          => $item->nilai_akademik,
                    'nilai_non_akademik'      => $item->nilai_non_akademik,
                    'nilai_keagamaan'         => $item->nilai_keagamaan,
                    'nilai_gtk'               => $item->nilai_gtk,
                    'nilai_lembaga'           => $item->nilai_lembaga,
                    'total_nilai_asesor'      => $item->total_nilai_asesor,
                    'potongan_aduan'          => $item->potongan_aduan,
                    'potongan_keterlambatan'  => $item->potongan_keterlambatan,
                    'total_nilai_akhir'       => $item->total_nilai_akhir,
                    'jumlah_prestasi_dinilai' => $item->jumlah_prestasi_dinilai,
                ]);
            }

            ActivityLogger::log(
                event: 'create',
                description: 'Mengarsipkan ranking periode ' . $periode,
                subject: $arsip,
                properties: [
                    'periode' => $periode,
                    'jumlah_madrasah' => $dataRanking->count(),
                ]
            );

            return $arsip;
        });

        return redirect()
            ->route('ranking-arsip.show', $arsip->id)
            ->with('success', 'Ranking periode ' . $periode . ' berhasil diarsipkan (' . $dataRanking->count() . ' madrasah).');
    }

    public function show(Request $request, RankingArsip $ranking_arsip)
    {
        $jenjangFilter = $request->query('jenjang');

        $daftarJenjangArsip = $ranking_arsip->details()
            ->whereNotNull('jenjang_madrasah')
            ->distinct()
            ->orderBy('jenjang_madrasah')
            ->pluck('jenjang_madrasah');

        $hasil = $this->hitungPapanArsipPerBidang($ranking_arsip, $jenjangFilter);

        $breadcrumb = breadcrumb([
            'Hasil & Ranking' => route('ranking.index'),
            'Arsip Ranking' => route('ranking-arsip.index'),
            'Periode ' . $ranking_arsip->periode
        ]);

        return view('ranking-arsip.show', compact(
            'ranking_arsip',
            'hasil',
            'daftarJenjangArsip',
            'jenjangFilter',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | BANGUN 5 PAPAN PER BIDANG (+ TABEL TOTAL REFERENSI) DARI DATA ARSIP
    |--------------------------------------------------------------------------
    | Sama konsepnya dengan RankingController::hitungRankingPerBidang() di
    | Ranking Live, cuma sumbernya data BEKU (RankingArsipDetail yang sudah
    | tersimpan), bukan hitung ulang dari penilaian_prestasis. Potongan per
    | bidang direkonstruksi dari kolom agregat yang tersimpan (potongan_aduan,
    | potongan_keterlambatan) memakai aturan yang sama seperti Dashboard &
    | Kelola: Keterlambatan dibagi rata 5 bidang, Aduan cuma menyunat Lembaga.
    |--------------------------------------------------------------------------
    */
    private function hitungPapanArsipPerBidang(RankingArsip $arsip, ?string $jenjangFilter): array
    {
        $baris = $arsip->details()
            ->when($jenjangFilter, fn ($q) => $q->where('jenjang_madrasah', $jenjangFilter))
            ->get();

        $rankingPerBidang = collect(self::PETA_KOLOM_BIDANG)->mapWithKeys(function ($kolom, $label) use ($baris) {

            $papan = $baris
                ->filter(fn ($row) => $row->$kolom > 0)
                ->map(function ($row) use ($kolom, $label) {
                    $potonganKeterlambatanBidang = round($row->potongan_keterlambatan / 5, 2);
                    $potonganAduanBidang = $label === 'Lembaga' ? $row->potongan_aduan : 0;
                    $totalPotonganBidang = round($potonganKeterlambatanBidang + $potonganAduanBidang, 2);

                    return (object) [
                        'madrasah_id'            => $row->madrasah_id,
                        'nama_madrasah'          => $row->nama_madrasah,
                        'npsn'                   => $row->npsn,
                        'jenjang_madrasah'       => $row->jenjang_madrasah,
                        'kota'                   => $row->kota,
                        'nilai_mentah'           => round($row->$kolom, 2),
                        'potongan_aduan'         => $potonganAduanBidang,
                        'potongan_keterlambatan' => $potonganKeterlambatanBidang,
                        'total_potongan'         => $totalPotonganBidang,
                        'nilai_akhir'            => round(max(0, $row->$kolom - $totalPotonganBidang), 2),
                    ];
                })
                ->sortByDesc('nilai_akhir')
                ->values()
                ->map(function ($row, $index) {
                    $row->peringkat = $index + 1;
                    return $row;
                });

            return [$label => $papan];
        });

        // Tabel referensi -- peringkat dihitung ULANG di lingkup yang
        // sedang tampil (bukan sekadar mengambil kolom 'peringkat'
        // tersimpan apa adanya), supaya nomornya tetap runtut 1..N waktu
        // difilter ke satu jenjang, bukan meloncat sesuai peringkat global.
        $total = $baris
            ->sortByDesc('total_nilai_akhir')
            ->values()
            ->map(function ($row, $index) {
                $row->peringkat_tampil = $index + 1;
                return $row;
            });

        return [
            'per_bidang' => $rankingPerBidang,
            'total'      => $total,
        ];
    }

    public function export(RankingArsip $ranking_arsip)
    {
        return Excel::download(
            new RankingArsipExport($ranking_arsip),
            'Arsip-Ranking-Periode-' . $ranking_arsip->periode . '.xlsx'
        );
    }

    public function destroy(RankingArsip $ranking_arsip)
    {
        $periode = $ranking_arsip->periode;

        ActivityLogger::log(
            event: 'delete',
            description: 'Menghapus arsip ranking periode ' . $periode,
            subject: $ranking_arsip
        );

        $ranking_arsip->delete(); // detail ikut terhapus (cascade)

        return redirect()
            ->route('ranking-arsip.index')
            ->with('success', 'Arsip ranking periode ' . $periode . ' berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | HITUNG DATA LENGKAP (RANKING + BREAKDOWN PER BIDANG + POTONGAN)
    |--------------------------------------------------------------------------
    | Method ini TERPISAH dari RankingController::index() secara sengaja --
    | supaya tidak menyentuh/berisiko merusak halaman Ranking (live) yang
    | sudah berjalan. Query di sini mengambil SELURUH madrasah FINISHED
    | (semua jenjang digabung), tidak menerima filter apapun, karena arsip
    | harus selalu lengkap.
    |--------------------------------------------------------------------------
    */
    private function hitungDataLengkap(int $periode): \Illuminate\Support\Collection
    {
        $madrasahIdsFinished = PrestasiSiklus::where('periode', $periode)
            ->where('status', PrestasiSiklus::FINISHED)
            ->pluck('madrasah_id');

        if ($madrasahIdsFinished->isEmpty()) {
            return collect();
        }

        // Nilai per (madrasah, bidang) -- satu query agregat untuk semua
        // madrasah sekaligus, bukan di-loop query satu-satu.
        $perBidang = DB::table('penilaian_prestasis')
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

        $madrasahs = Madrasah::whereIn('id', $madrasahIdsFinished)->get();

        return $madrasahs
            ->map(function ($madrasah) use ($perBidang, $periode) {

                $barisBidang = $perBidang->get($madrasah->id, collect());

                $nilaiPerBidang = [
                    'nilai_akademik'     => 0.0,
                    'nilai_non_akademik' => 0.0,
                    'nilai_keagamaan'    => 0.0,
                    'nilai_gtk'          => 0.0,
                    'nilai_lembaga'      => 0.0,
                ];

                // Versi keyed by LABEL bidang (bukan nama kolom) -- format
                // yang diharapkan PenguranganPoinService::hitungSetelahPotonganPerBidang()
                $nilaiPerBidangLabel = [];

                $jumlahDinilai = 0;

                foreach ($barisBidang as $baris) {
                    $kolom = self::PETA_KOLOM_BIDANG[$baris->bidang_prestasi] ?? null;

                    if ($kolom) {
                        $nilaiPerBidang[$kolom] = round((float) $baris->total_nilai, 2);
                    }

                    $jumlahDinilai += (int) $baris->jumlah;
                }

                foreach (self::PETA_KOLOM_BIDANG as $label => $kolom) {
                    $nilaiPerBidangLabel[$label] = $nilaiPerBidang[$kolom];
                }

                $totalSebelumPotongan = round(array_sum($nilaiPerBidang), 2);

                /*
                |--------------------------------------------------------------------------
                | Potongan dihitung PER BIDANG (JMA menentukan juara per Bidang,
                | jadi Aduan Masyarakat & jatah Keterlambatan juga dipecah per
                | bidang -- Lembaga jadi satu-satunya yang kena keduanya).
                | Aggregat-nya (potongan_aduan, potongan_keterlambatan,
                | total_nilai_akhir) TETAP disimpan seperti skema lama -- cuma
                | dijumlahkan dari hasil per-bidang, bukan dihitung langsung
                | dari total gabungan seperti sebelumnya. Hasil akhirnya
                | matematis identik.
                |--------------------------------------------------------------------------
                */
                $hasilPotongan = $this->penguranganPoinService->hitungSetelahPotonganPerBidang(
                    $madrasah->id,
                    $periode,
                    $nilaiPerBidangLabel
                );

                return (object) array_merge($nilaiPerBidang, [
                    'madrasah_id'             => $madrasah->id,
                    'nama_madrasah'           => $madrasah->nama_madrasah,
                    'npsn'                    => $madrasah->npsn,
                    'jenjang_madrasah'        => $madrasah->jenjang_madrasah,
                    'kota'                    => $madrasah->kota,
                    'total_nilai_asesor'      => $totalSebelumPotongan,
                    'potongan_aduan'          => $hasilPotongan['per_bidang']['Lembaga']['potongan_aduan'],
                    'potongan_keterlambatan'  => round(
                        collect($hasilPotongan['per_bidang'])->sum('potongan_keterlambatan'),
                        2
                    ),
                    'total_nilai_akhir'       => $hasilPotongan['total_nilai_akhir'],
                    'jumlah_prestasi_dinilai' => $jumlahDinilai,
                ]);
            })
            ->sortByDesc('total_nilai_akhir')
            ->values()
            ->map(function ($item, $index) {
                $item->peringkat = $index + 1;
                return $item;
            });
    }
}