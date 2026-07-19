<?php

namespace App\Http\Controllers;

use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;

class DashboardMadrasahController extends Controller
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

    private const WARNA_TAHUN = ['#1d4ed8', '#38bdf8', '#f59e0b', '#8b5cf6', '#10b981', '#ef4444'];

    private const WARNA_JUARA = ['#1d4ed8', '#38bdf8', '#f59e0b', '#8b5cf6', '#10b981', '#94a3b8'];

    private const NAMA_BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    /*
    |--------------------------------------------------------------------------
    | JUMLAH TAHUN YANG DITAMPILKAN DI GRAFIK/TABEL MULTI-TAHUN
    |--------------------------------------------------------------------------
    */
    private const JUMLAH_TAHUN_TREN = 3;

    /*
    |--------------------------------------------------------------------------
    | CATATAN PENTING: SELURUH KOMPONEN DI DASHBOARD INI SENGAJA HANYA
    | MEMAKAI DATA MENTAH (jumlah/komposisi dari prestasi_siswas) --
    | TIDAK ADA yang menyentuh nilai_akhir/persentase hasil penilaian
    | asesor. Ini keputusan sengaja supaya madrasah tidak melihat nilai
    | yang berpotensi jadi bahan sanggahan/protes, tapi tetap dapat bahan
    | evaluasi lewat pola partisipasi & komposisi.
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $periodeDipilih = $request->integer('periode') ?: PeriodeAktif::aktif();

        /*
        |--------------------------------------------------------------------------
        | DAFTAR PERIODE UNTUK DROPDOWN
        |--------------------------------------------------------------------------
        */
        $daftarPeriode = PrestasiSiswa::visible()
            ->select('periode')
            ->distinct()
            ->pluck('periode');

        if (! $daftarPeriode->contains(PeriodeAktif::aktif())) {
            $daftarPeriode->push(PeriodeAktif::aktif());
        }

        $daftarPeriode = $daftarPeriode->sortDesc()->values();

        /*
        |--------------------------------------------------------------------------
        | RENTANG TAHUN UNTUK GRAFIK/TABEL MULTI-TAHUN
        |--------------------------------------------------------------------------
        */
        $tahunRentang = $daftarPeriode
            ->filter(fn ($p) => $p <= $periodeDipilih)
            ->sort()
            ->values()
            ->slice(-self::JUMLAH_TAHUN_TREN)
            ->values();

        if ($tahunRentang->isEmpty()) {
            $tahunRentang = collect([$periodeDipilih]);
        }

        /*
        |--------------------------------------------------------------------------
        | 1. STAT CARDS — scoped ke periode yang sedang dipilih saja
        |--------------------------------------------------------------------------
        */
        $dataPeriodeIni = PrestasiSiswa::visible()
            ->where('periode', $periodeDipilih)
            ->get();

        $totalPrestasi  = $dataPeriodeIni->count();
        $bidangDipakai  = $dataPeriodeIni->pluck('bidang_prestasi')->unique()->count();
        $tingkatDipakai = $dataPeriodeIni->pluck('tingkat')->unique()->count();
        $jenisKegiatan  = $dataPeriodeIni->pluck('nama_kegiatan')->unique()->count();
        $totalJuara1    = $dataPeriodeIni->where('juara', 'Juara 1')->count();

        /*
        |--------------------------------------------------------------------------
        | 2. TREN TOTAL PRESTASI (multi-tahun)
        |--------------------------------------------------------------------------
        */
        $trenPerTahun = PrestasiSiswa::visible()
            ->whereIn('periode', $tahunRentang)
            ->selectRaw('periode, COUNT(*) as total')
            ->groupBy('periode')
            ->pluck('total', 'periode');

        $trenTotalPrestasi = $tahunRentang->map(fn ($tahun) => $trenPerTahun->get($tahun, 0));

        /*
        |--------------------------------------------------------------------------
        | 3. KOMPOSISI BIDANG PRESTASI (donut, periode dipilih saja)
        |--------------------------------------------------------------------------
        */
        $komposisiBidang = collect(self::URUTAN_BIDANG)
            ->map(function ($bidang) use ($dataPeriodeIni, $totalPrestasi) {
                $jumlah = $dataPeriodeIni->where('bidang_prestasi', $bidang)->count();

                return [
                    'label'  => $bidang,
                    'jumlah' => $jumlah,
                    'persen' => $totalPrestasi > 0 ? round($jumlah / $totalPrestasi * 100) : 0,
                    'warna'  => self::WARNA_BIDANG[$bidang],
                ];
            })
            ->filter(fn ($item) => $item['jumlah'] > 0)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 4. PRESTASI BERDASARKAN TINGKAT (grouped bar, multi-tahun)
        |--------------------------------------------------------------------------
        */
        $dataTingkatMultiTahun = PrestasiSiswa::visible()
            ->whereIn('periode', $tahunRentang)
            ->selectRaw('periode, tingkat, COUNT(*) as total')
            ->groupBy('periode', 'tingkat')
            ->get();

        $tingkatPerTahun = $tahunRentang->mapWithKeys(function ($tahun) use ($dataTingkatMultiTahun) {
            return [
                $tahun => collect(self::URUTAN_TINGKAT)->map(function ($tingkat) use ($tahun, $dataTingkatMultiTahun) {
                    return $dataTingkatMultiTahun
                        ->first(fn ($row) => $row->periode == $tahun && $row->tingkat === $tingkat)
                        ?->total ?? 0;
                })->values()
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | 5. KOMPOSISI JUARA (donut, periode dipilih, kategori DINAMIS)
        |--------------------------------------------------------------------------
        */
        $komposisiJuara = $dataPeriodeIni
            ->groupBy('juara')
            ->map(fn ($items, $juara) => [
                'label'  => $juara ?: 'Tidak diketahui',
                'jumlah' => $items->count(),
                'persen' => $totalPrestasi > 0 ? round($items->count() / $totalPrestasi * 100) : 0,
            ])
            ->sortByDesc('jumlah')
            ->values()
            ->take(6)
            ->map(function ($item, $index) {
                $item['warna'] = self::WARNA_JUARA[$index] ?? '#cbd5e1';
                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | 6. RINGKASAN PRESTASI PER BIDANG (tabel matrix bidang x tahun)
        |--------------------------------------------------------------------------
        */
        $dataBidangMultiTahun = PrestasiSiswa::visible()
            ->whereIn('periode', $tahunRentang)
            ->selectRaw('periode, bidang_prestasi, COUNT(*) as total')
            ->groupBy('periode', 'bidang_prestasi')
            ->get();

        $ringkasanBidang = collect(self::URUTAN_BIDANG)->map(function ($bidang) use ($tahunRentang, $dataBidangMultiTahun) {
            $perTahun = $tahunRentang->mapWithKeys(function ($tahun) use ($bidang, $dataBidangMultiTahun) {
                $jumlah = $dataBidangMultiTahun
                    ->first(fn ($row) => $row->periode == $tahun && $row->bidang_prestasi === $bidang)
                    ?->total ?? 0;

                return [$tahun => $jumlah];
            });

            return [
                'bidang'    => $bidang,
                'per_tahun' => $perTahun,
                'total'     => $perTahun->sum(),
            ];
        });

        $totalPerTahun = $tahunRentang->mapWithKeys(function ($tahun) use ($ringkasanBidang) {
            return [$tahun => $ringkasanBidang->sum(fn ($row) => $row['per_tahun'][$tahun])];
        });

        $totalKeseluruhan = $totalPerTahun->sum();

        /*
        |--------------------------------------------------------------------------
        | 7. TOP 10 KEGIATAN PENYUMBANG PRESTASI (periode dipilih)
        |--------------------------------------------------------------------------
        */
        $topKegiatan = $dataPeriodeIni
            ->groupBy('nama_kegiatan')
            ->map(fn ($items, $nama) => [
                'nama'    => $nama,
                'bidang'  => $items->first()->bidang_prestasi,
                'tingkat' => $items->first()->tingkat,
                'jumlah'  => $items->count(),
            ])
            ->sortByDesc('jumlah')
            ->values()
            ->take(10);

        $maxJumlahKegiatan = $topKegiatan->max('jumlah') ?: 1;

        /*
        |--------------------------------------------------------------------------
        | 8. CROSS-TAB BIDANG x TINGKAT (BARU)
        |--------------------------------------------------------------------------
        | Supaya kelihatan per bidang mentoknya di tingkat apa -- bukan cuma
        | gabungan semua bidang seperti komponen #4.
        |--------------------------------------------------------------------------
        */
        $crosstabBidangTingkat = collect(self::URUTAN_BIDANG)
            ->map(function ($bidang) use ($dataPeriodeIni) {
                $subsetBidang = $dataPeriodeIni->where('bidang_prestasi', $bidang);

                $perTingkat = collect(self::URUTAN_TINGKAT)->mapWithKeys(function ($tingkat) use ($subsetBidang) {
                    return [$tingkat => $subsetBidang->where('tingkat', $tingkat)->count()];
                });

                return [
                    'bidang'      => $bidang,
                    'per_tingkat' => $perTingkat,
                    'total'       => $perTingkat->sum(),
                ];
            })
            ->filter(fn ($row) => $row['total'] > 0)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 9. KOMPOSISI INDIVIDU vs BEREGU (BARU)
        |--------------------------------------------------------------------------
        */
        $komposisiKategori = $dataPeriodeIni
            ->groupBy('kategori_kegiatan')
            ->map(fn ($items, $kategori) => [
                'label'  => $kategori ?: 'Tidak diketahui',
                'jumlah' => $items->count(),
                'persen' => $totalPrestasi > 0 ? round($items->count() / $totalPrestasi * 100) : 0,
            ])
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 10. KOMPOSISI LURING vs DARING (BARU)
        |--------------------------------------------------------------------------
        */
        $komposisiMetode = $dataPeriodeIni
            ->groupBy('metode_pelaksanaan')
            ->map(fn ($items, $metode) => [
                'label'  => $metode ?: 'Tidak diketahui',
                'jumlah' => $items->count(),
                'persen' => $totalPrestasi > 0 ? round($items->count() / $totalPrestasi * 100) : 0,
            ])
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 11. DISTRIBUSI BULANAN KEGIATAN (BARU) — buat perencanaan kalender
        |--------------------------------------------------------------------------
        */
        $distribusiBulanMentah = $dataPeriodeIni->groupBy(fn ($item) => $item->waktu_kegiatan?->format('n'));

        $distribusiBulan = collect(range(1, 12))->map(function ($bulanIndex) use ($distribusiBulanMentah) {
            return [
                'label'  => self::NAMA_BULAN[$bulanIndex - 1],
                'jumlah' => $distribusiBulanMentah->get((string) $bulanIndex, collect())->count(),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | 12. TOP 5 LEMBAGA PENYELENGGARA (BARU) — cek konsentrasi sumber lomba
        |--------------------------------------------------------------------------
        */
        $topLembaga = $dataPeriodeIni
            ->groupBy('lembaga_penyelenggara')
            ->map(fn ($items, $lembaga) => [
                'lembaga' => $lembaga ?: 'Tidak diketahui',
                'jumlah'  => $items->count(),
                'persen'  => $totalPrestasi > 0 ? round($items->count() / $totalPrestasi * 100) : 0,
            ])
            ->sortByDesc('jumlah')
            ->values()
            ->take(5);

        /*
        |--------------------------------------------------------------------------
        | 13. BENCHMARK VOLUME DENGAN SESAMA JENJANG (BARU)
        |--------------------------------------------------------------------------
        | SENGAJA cuma bandingkan JUMLAH prestasi (partisipasi), BUKAN nilai/
        | kualitas -- supaya tidak menyentuh hasil penilaian asesor sama sekali.
        |--------------------------------------------------------------------------
        */
        $madrasahSaya = auth()->user()->madrasah;
        $jenjangSaya = $madrasahSaya->jenjang_madrasah ?? null;

        $benchmarkJenjang = null;

        if ($jenjangSaya) {

            $madrasahIdsSejenjang = Madrasah::where('jenjang_madrasah', $jenjangSaya)->pluck('id');

            $totalPrestasiSejenjang = PrestasiSiswa::whereIn('madrasah_id', $madrasahIdsSejenjang)
                ->where('periode', $periodeDipilih)
                ->count();

            $jumlahMadrasahSejenjang = $madrasahIdsSejenjang->count();

            $rataRataSejenjang = $jumlahMadrasahSejenjang > 0
                ? round($totalPrestasiSejenjang / $jumlahMadrasahSejenjang, 1)
                : 0;

            $benchmarkJenjang = [
                'jenjang'                  => $jenjangSaya,
                'total_saya'               => $totalPrestasi,
                'rata_rata_sejenjang'      => $rataRataSejenjang,
                'jumlah_madrasah_sejenjang' => $jumlahMadrasahSejenjang,
                'selisih'                  => round($totalPrestasi - $rataRataSejenjang, 1),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 14. INSIGHT — rule-based sederhana (BUKAN AI), murni perbandingan angka
        |--------------------------------------------------------------------------
        */
        $insight = $this->buildInsight(
            $komposisiBidang,
            $dataTingkatMultiTahun,
            $trenTotalPrestasi,
            $tahunRentang,
            $ringkasanBidang,
            $crosstabBidangTingkat,
            $komposisiKategori,
            $benchmarkJenjang
        );

        return view('dashboard.madrasah', compact(
            'periodeDipilih',
            'daftarPeriode',
            'tahunRentang',
            'totalPrestasi',
            'bidangDipakai',
            'tingkatDipakai',
            'jenisKegiatan',
            'totalJuara1',
            'trenTotalPrestasi',
            'komposisiBidang',
            'tingkatPerTahun',
            'komposisiJuara',
            'ringkasanBidang',
            'totalPerTahun',
            'totalKeseluruhan',
            'topKegiatan',
            'maxJumlahKegiatan',
            'crosstabBidangTingkat',
            'komposisiKategori',
            'komposisiMetode',
            'distribusiBulan',
            'topLembaga',
            'benchmarkJenjang',
            'insight'
        ));
    }

    private function buildInsight(
            $komposisiBidang,
            $dataTingkatMultiTahun,
            $trenTotalPrestasi,
            $tahunRentang,
            $ringkasanBidang,
            $crosstabBidangTingkat,
            $komposisiKategori,
            $benchmarkJenjang
        ): array {
        $insight = [];

        // Bidang penyumbang terbesar
        $bidangTerbesar = $komposisiBidang->sortByDesc('jumlah')->first();
        if ($bidangTerbesar) {
            $insight[] = [
                'icon' => 'bi-trophy',
                'text' => "Bidang <strong>{$bidangTerbesar['label']}</strong> menjadi penyumbang prestasi terbesar.",
            ];
        }

        // Tingkat paling dominan (akumulasi seluruh rentang tahun)
        $tingkatTerbesar = $dataTingkatMultiTahun->groupBy('tingkat')
            ->map(fn ($rows) => $rows->sum('total'))
            ->sortDesc();

        if ($tingkatTerbesar->isNotEmpty() && $tingkatTerbesar->first() > 0) {
            $insight[] = [
                'icon' => 'bi-bullseye',
                'text' => "Prestasi tingkat <strong>{$tingkatTerbesar->keys()->first()}</strong> paling dominan.",
            ];
        }

        // Tahun dengan capaian terbaik
        if ($trenTotalPrestasi->isNotEmpty() && $trenTotalPrestasi->max() > 0) {
            $tahunTerbaikIndex = $trenTotalPrestasi->search($trenTotalPrestasi->max());
            $tahunTerbaik = $tahunRentang->get($tahunTerbaikIndex);

            $insight[] = [
                'icon' => 'bi-star',
                'text' => "Tahun <strong>{$tahunTerbaik}</strong> merupakan periode dengan capaian terbaik.",
            ];
        }

        // Bidang dengan pertumbuhan positif terbesar (tahun terakhir vs sebelumnya)
        if ($tahunRentang->count() >= 2) {
            $tahunSekarang = $tahunRentang->last();
            $tahunSebelum = $tahunRentang->get($tahunRentang->count() - 2);

            $pertumbuhanTerbesar = $ringkasanBidang
                ->map(fn ($row) => [
                    'bidang'  => $row['bidang'],
                    'selisih' => $row['per_tahun'][$tahunSekarang] - $row['per_tahun'][$tahunSebelum],
                ])
                ->sortByDesc('selisih')
                ->first();

            if ($pertumbuhanTerbesar && $pertumbuhanTerbesar['selisih'] > 0) {
                $insight[] = [
                    'icon' => 'bi-graph-up-arrow',
                    'text' => "Bidang <strong>{$pertumbuhanTerbesar['bidang']}</strong> mengalami perkembangan positif dibanding tahun sebelumnya.",
                ];
            }
        }

        // BARU: Bidang yang belum pernah tembus tingkat Nasional/Internasional
        $bidangBelumTinggi = $crosstabBidangTingkat->first(function ($row) {
            $tinggi = ($row['per_tingkat']['Nasional'] ?? 0) + ($row['per_tingkat']['Internasional'] ?? 0);
            return $row['total'] > 0 && $tinggi === 0;
        });

        if ($bidangBelumTinggi) {
            $insight[] = [
                'icon' => 'bi-flag',
                'text' => "Bidang <strong>{$bidangBelumTinggi['bidang']}</strong> belum pernah tembus tingkat Nasional/Internasional pada periode ini.",
            ];
        }

        // BARU: Komposisi Individu vs Beregu dominan
        $kategoriDominan = $komposisiKategori->sortByDesc('jumlah')->first();
        if ($kategoriDominan && $kategoriDominan['jumlah'] > 0) {
            $insight[] = [
                'icon' => 'bi-people',
                'text' => "Mayoritas prestasi ({$kategoriDominan['persen']}%) berasal dari kategori <strong>{$kategoriDominan['label']}</strong>.",
            ];
        }

        // BARU: Benchmark dengan sesama jenjang
        if ($benchmarkJenjang && $benchmarkJenjang['jumlah_madrasah_sejenjang'] > 1) {
            if ($benchmarkJenjang['selisih'] > 0) {
                $insight[] = [
                    'icon' => 'bi-graph-up',
                    'text' => "Total prestasi Anda <strong>{$benchmarkJenjang['selisih']} lebih banyak</strong> dari rata-rata madrasah sejenjang {$benchmarkJenjang['jenjang']}.",
                ];
            } elseif ($benchmarkJenjang['selisih'] < 0) {
                $insight[] = [
                    'icon' => 'bi-graph-down',
                    'text' => "Total prestasi Anda <strong>" . abs($benchmarkJenjang['selisih']) . " di bawah</strong> rata-rata madrasah sejenjang {$benchmarkJenjang['jenjang']}.",
                ];
            }
        }

        return $insight;
    }
}