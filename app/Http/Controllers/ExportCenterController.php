<?php

namespace App\Http\Controllers;

use App\Exports\DashboardExport;
use App\Models\Madrasah;
use App\Models\PrestasiSiswa;
use App\Models\PrestasiSiklus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportCenterController extends Controller
{
    private const PETA_KOLOM_BIDANG = [
        'Akademik'     => 'nilai_akademik',
        'Non Akademik' => 'nilai_non_akademik',
        'Keagamaan'    => 'nilai_keagamaan',
        'GTK'          => 'nilai_gtk',
        'Lembaga'      => 'nilai_lembaga',
    ];

    /*
    |--------------------------------------------------------------------------
    | HALAMAN FILTER
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $daftarPeriode = PrestasiSiswa::select('periode')
            ->distinct()
            ->orderByDesc('periode')
            ->pluck('periode');

        $daftarJenjang = Madrasah::whereNotNull('jenjang_madrasah')
            ->distinct()
            ->orderBy('jenjang_madrasah')
            ->pluck('jenjang_madrasah');

        $daftarBidang = array_keys(self::PETA_KOLOM_BIDANG);

        $daftarKota = Madrasah::whereNotNull('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');

        $daftarStatus = Madrasah::whereNotNull('status_madrasah')
            ->distinct()
            ->orderBy('status_madrasah')
            ->pluck('status_madrasah');

        $breadcrumb = breadcrumb(['Pusat Unduh Data']);

        return view('export-center.index', compact(
            'daftarPeriode',
            'daftarJenjang',
            'daftarBidang',
            'daftarKota',
            'daftarStatus',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT -- satu pintu masuk, dibedakan lewat parameter 'tipe'.
    | Filter (periode/jenjang/bidang/kota/status) dibaca sama persis untuk
    | semua tipe -- konsisten sama apa yang dipilih di halaman filter.
    |--------------------------------------------------------------------------
    */
    public function export(Request $request)
    {
        $tipe = $request->query('tipe');

        $filter = [
            'periode' => $request->query('periode'),
            'jenjang' => $request->query('jenjang'),
            'bidang'  => $request->query('bidang'),
            'kota'    => $request->query('kota'),
            'status'  => $request->query('status'),
        ];

        return match ($tipe) {
            'prestasi-mentah'  => $this->exportPrestasiMentah($filter),
            'hasil-penilaian'  => $this->exportHasilPenilaian($filter),
            'peringkat'        => $this->exportPeringkat($filter),
            'tren'             => $this->exportTren($filter),
            default            => abort(404),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Filter madrasah yang dipakai berulang (jenjang, kota, status) --
    | dijadikan closure supaya bisa dipakai baik lewat whereHas() maupun
    | langsung ke query Madrasah.
    |--------------------------------------------------------------------------
    */
    private function filterMadrasah(array $filter): \Closure
    {
        return function ($query) use ($filter) {
            $query->when($filter['jenjang'], fn ($q) => $q->where('jenjang_madrasah', $filter['jenjang']))
                ->when($filter['kota'], fn ($q) => $q->where('kota', $filter['kota']))
                ->when($filter['status'], fn ($q) => $q->where('status_madrasah', $filter['status']));
        };
    }

    /*
    |--------------------------------------------------------------------------
    | 1. DATA PRESTASI MENTAH -- semua prestasi apa adanya, belum ada
    | campur tangan penilaian asesor sama sekali.
    |--------------------------------------------------------------------------
    */
    private function exportPrestasiMentah(array $filter)
    {
        $data = PrestasiSiswa::with('madrasah')
            ->when($filter['periode'], fn ($q) => $q->where('periode', $filter['periode']))
            ->when($filter['bidang'], fn ($q) => $q->where('bidang_prestasi', $filter['bidang']))
            ->whereHas('madrasah', $this->filterMadrasah($filter))
            ->get()
            ->map(fn ($p) => [
                $p->madrasah->nama_madrasah ?? '-',
                $p->madrasah->jenjang_madrasah ?? '-',
                $p->madrasah->kota ?? '-',
                $p->madrasah->status_madrasah ?? '-',
                $p->bidang_prestasi,
                $p->nama_kegiatan,
                $p->tingkat,
                $p->kategori_kegiatan,
                $p->juara,
                $p->lembaga_penyelenggara,
                $p->kategori_penyelenggara,
                optional($p->waktu_kegiatan)->format('d-m-Y'),
                $p->metode_pelaksanaan,
                $p->skor,
                $p->link_drive_bukti,
                $p->periode,
            ]);

        $headings = [
            'Madrasah', 'Jenjang', 'Kota', 'Status Madrasah', 'Bidang', 'Nama Kegiatan',
            'Tingkat', 'Kategori Kegiatan', 'Juara', 'Lembaga Penyelenggara',
            'Kategori Penyelenggara', 'Waktu Kegiatan', 'Metode', 'Skor', 'Link Bukti', 'Periode',
        ];

        return Excel::download(
            new DashboardExport($data, $headings, 'Data Prestasi Mentah'),
            'Data-Prestasi-Mentah.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 2. DATA HASIL PENILAIAN -- prestasi + hasil dari asesor (skor mentah
    | Madrasah BERDAMPINGAN dengan persentase & nilai akhir dari Asesor).
    |
    | CATATAN JUJUR: kolom "nama Asesor yang menilai" SENGAJA tidak saya
    | masukkan -- saya tidak yakin 100% nama kolom/relasi yang menyimpan
    | siapa penilainya di model PenilaianPrestasi (bisa jadi asesor_id,
    | user_id, atau lewat tabel AssignAsesor terpisah). Daripada saya
    | tebak dan salah, saya sertakan data yang saya YAKIN strukturnya
    | (persentase, nilai_akhir, catatan, status). Kalau mau ditambah nama
    | asesornya, kasih tahu saya nama kolom/relasinya yang benar.
    |--------------------------------------------------------------------------
    */
    private function exportHasilPenilaian(array $filter)
    {
        $data = PrestasiSiswa::with(['madrasah', 'penilaianPrestasi'])
            ->when($filter['periode'], fn ($q) => $q->where('periode', $filter['periode']))
            ->when($filter['bidang'], fn ($q) => $q->where('bidang_prestasi', $filter['bidang']))
            ->whereHas('madrasah', $this->filterMadrasah($filter))
            ->get()
            ->map(function ($p) {
                $penilaian = $p->penilaianPrestasi;

                return [
                    $p->madrasah->nama_madrasah ?? '-',
                    $p->madrasah->jenjang_madrasah ?? '-',
                    $p->madrasah->kota ?? '-',
                    $p->bidang_prestasi,
                    $p->nama_kegiatan,
                    $p->tingkat,
                    $p->juara,
                    $p->skor, // skor mentah -- apa adanya dari Madrasah
                    $penilaian->persentase ?? null,
                    $penilaian->nilai_akhir ?? null,
                    $penilaian->catatan ?? '-',
                    $penilaian && $penilaian->status === 'completed' ? 'Sudah Dinilai' : 'Belum Dinilai',
                    $p->periode,
                ];
            });

        $headings = [
            'Madrasah', 'Jenjang', 'Kota', 'Bidang', 'Nama Kegiatan', 'Tingkat', 'Juara',
            'Skor Mentah', 'Persentase Asesor', 'Nilai Akhir', 'Catatan Asesor', 'Status Penilaian', 'Periode',
        ];

        return Excel::download(
            new DashboardExport($data, $headings, 'Data Hasil Penilaian'),
            'Data-Hasil-Penilaian.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 3. PERINGKAT -- pola perhitungannya SAMA PERSIS dengan
    | RankingArsipController::hitungDataLengkap() (per-bidang, potongan
    | Aduan+Keterlambatan), cuma di sini bisa difilter jenjang/kota/status
    | juga (Ranking Live/Arsip yang sudah ada tidak punya filter itu).
    |--------------------------------------------------------------------------
    */
    private function exportPeringkat(array $filter)
    {
        $periode = $filter['periode'] ?? \App\Models\PeriodeAktif::aktif();

        $madrasahIdsFinished = PrestasiSiklus::where('periode', $periode)
            ->where('status', PrestasiSiklus::FINISHED)
            ->pluck('madrasah_id');

        $madrasahs = Madrasah::whereIn('id', $madrasahIdsFinished)
            ->when($filter['jenjang'], fn ($q) => $q->where('jenjang_madrasah', $filter['jenjang']))
            ->when($filter['kota'], fn ($q) => $q->where('kota', $filter['kota']))
            ->when($filter['status'], fn ($q) => $q->where('status_madrasah', $filter['status']))
            ->get();

        $perBidang = DB::table('penilaian_prestasis')
            ->join('prestasi_siswas', 'prestasi_siswas.id', '=', 'penilaian_prestasis.prestasi_siswa_id')
            ->where('penilaian_prestasis.status', 'completed')
            ->whereIn('prestasi_siswas.madrasah_id', $madrasahs->pluck('id'))
            ->where('prestasi_siswas.periode', $periode)
            ->when($filter['bidang'], fn ($q) => $q->where('prestasi_siswas.bidang_prestasi', $filter['bidang']))
            ->groupBy('prestasi_siswas.madrasah_id', 'prestasi_siswas.bidang_prestasi')
            ->selectRaw('prestasi_siswas.madrasah_id, prestasi_siswas.bidang_prestasi, SUM(penilaian_prestasis.nilai_akhir) as total_nilai')
            ->get()
            ->groupBy('madrasah_id');

        $data = $madrasahs->map(function ($madrasah) use ($perBidang) {
            $barisBidang = $perBidang->get($madrasah->id, collect());
            $nilaiPerBidang = array_fill_keys(self::PETA_KOLOM_BIDANG, 0.0);

            foreach ($barisBidang as $baris) {
                if (isset(self::PETA_KOLOM_BIDANG[$baris->bidang_prestasi])) {
                    $nilaiPerBidang[self::PETA_KOLOM_BIDANG[$baris->bidang_prestasi]] = round((float) $baris->total_nilai, 2);
                }
            }

            $total = round(array_sum($nilaiPerBidang), 2);

            return (object) array_merge($nilaiPerBidang, [
                'nama_madrasah' => $madrasah->nama_madrasah,
                'jenjang_madrasah' => $madrasah->jenjang_madrasah,
                'kota' => $madrasah->kota,
                'total' => $total,
            ]);
        })
            ->sortByDesc('total')
            ->values()
            ->map(function ($item, $i) {
                $item->peringkat = $i + 1;
                return $item;
            })
            ->map(fn ($item) => [
                $item->peringkat,
                $item->nama_madrasah,
                $item->jenjang_madrasah,
                $item->kota,
                $item->nilai_akademik,
                $item->nilai_non_akademik,
                $item->nilai_keagamaan,
                $item->nilai_gtk,
                $item->nilai_lembaga,
                $item->total,
            ]);

        $headings = [
            'Peringkat', 'Madrasah', 'Jenjang', 'Kota',
            'Akademik', 'Non Akademik', 'Keagamaan', 'GTK', 'Lembaga', 'Total',
        ];

        return Excel::download(
            new DashboardExport($data, $headings, 'Peringkat Periode ' . $periode),
            'Peringkat-Periode-' . $periode . '.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 4. TREN -- total & rata-rata nilai akhir per periode, dari data yang
    | SUDAH dinilai (bukan skor mentah), difilter jenjang/kota/status.
    | TIDAK bergantung ke Arsip Ranking -- dihitung langsung dari
    | penilaian_prestasis, supaya tetap jalan walau belum pernah diarsipkan.
    |--------------------------------------------------------------------------
    */
    private function exportTren(array $filter)
    {
        $daftarPeriode = PrestasiSiswa::select('periode')->distinct()->orderBy('periode')->pluck('periode');

        $data = $daftarPeriode->map(function ($periode) use ($filter) {
            $agg = DB::table('penilaian_prestasis')
                ->join('prestasi_siswas', 'prestasi_siswas.id', '=', 'penilaian_prestasis.prestasi_siswa_id')
                ->join('madrasahs', 'madrasahs.id', '=', 'prestasi_siswas.madrasah_id')
                ->where('penilaian_prestasis.status', 'completed')
                ->where('prestasi_siswas.periode', $periode)
                ->when($filter['bidang'], fn ($q) => $q->where('prestasi_siswas.bidang_prestasi', $filter['bidang']))
                ->when($filter['jenjang'], fn ($q) => $q->where('madrasahs.jenjang_madrasah', $filter['jenjang']))
                ->when($filter['kota'], fn ($q) => $q->where('madrasahs.kota', $filter['kota']))
                ->when($filter['status'], fn ($q) => $q->where('madrasahs.status_madrasah', $filter['status']))
                ->selectRaw('SUM(penilaian_prestasis.nilai_akhir) as total, AVG(penilaian_prestasis.nilai_akhir) as rata, COUNT(DISTINCT prestasi_siswas.madrasah_id) as jumlah_madrasah')
                ->first();

            return [
                $periode,
                (int) ($agg->jumlah_madrasah ?? 0),
                round($agg->total ?? 0, 2),
                round($agg->rata ?? 0, 2),
            ];
        });

        $headings = ['Periode', 'Jumlah Madrasah', 'Total Nilai', 'Rata-rata Nilai'];

        return Excel::download(
            new DashboardExport($data, $headings, 'Tren Nilai'),
            'Tren-Nilai.xlsx'
        );
    }
}