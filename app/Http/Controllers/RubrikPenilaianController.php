<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\RubrikPenilaian;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RubrikPenilaianController extends Controller
{
    private const BIDANG_LIST = ['Akademik', 'Non Akademik', 'Keagamaan', 'GTK', 'Lembaga'];
    private const JENIS_RUBRIK_LIST = ['Lomba', 'Karya', 'Kelembagaan', 'Hafalan'];
    private const TINGKAT_LIST = ['Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'];
    private const JUARA_LIST = ['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Harapan 2'];
    private const KATEGORI_KEGIATAN_LIST = ['Individu', 'Beregu'];
    private const METODE_LIST = ['Luring', 'Daring'];
    private const PENYELENGGARA_LIST = ['Pemerintah', 'Non Pemerintah'];

    public function index(Request $request)
    {
        $sedangCari = $request->filled('search');

        $queryDasar = RubrikPenilaian::query()
            ->when($request->filled('bidang_prestasi'), fn ($q) => $q->where('bidang_prestasi', $request->bidang_prestasi))
            ->when($request->filled('jenis_rubrik'), fn ($q) => $q->where('jenis_rubrik', $request->jenis_rubrik))
            ->when($request->filled('tahun_berlaku'), fn ($q) => $q->where('tahun_berlaku', $request->tahun_berlaku))
            ->when($sedangCari, function ($q) use ($request) {
                // Pakai REGEXP dengan batas kata (\b) -- bukan LIKE '%...%' biasa.
                // LIKE substring bikin cari "3 Juz" ikut nangkep "13 Juz" dan
                // "23 Juz" (karena keduanya SECARA HARFIAH mengandung teks
                // "3 Juz" di dalamnya). \b memastikan "3" yang dicari harus
                // benar-benar AWAL kata, bukan potongan dari angka lain.
                $keyword = trim($request->search);
                $pola = '\\b' . preg_quote($keyword, '/') . '\\b';

                $q->where(function ($sub) use ($pola) {
                    $sub->whereRaw('kriteria_khusus REGEXP ?', [$pola])
                        ->orWhereRaw('juara REGEXP ?', [$pola])
                        ->orWhereRaw('tingkat REGEXP ?', [$pola])
                        ->orWhereRaw('keterangan REGEXP ?', [$pola]);
                });
            });

        $daftarRubrik = null;
        $rubrikTerkelompok = null;

        if ($sedangCari) {
            /*
            |--------------------------------------------------------------------------
            | MODE CARI -- tabel flat + paginasi seperti semula. Pencarian teks
            | tidak cocok ditampilkan berkelompok (hasil bisa nyebar dari
            | berbagai Tingkat/Juara/Bidang sekaligus).
            |--------------------------------------------------------------------------
            */
            $daftarRubrik = $queryDasar
                ->orderBy('bidang_prestasi')
                ->orderBy('jenis_rubrik')
                ->orderByDesc('tahun_berlaku')
                ->orderBy('tingkat')
                ->paginate(30)
                ->withQueryString();
        } else {
            /*
            |--------------------------------------------------------------------------
            | MODE BROWSE (default) -- dikelompokkan & di-"pivot" persis format
            | tabel resmi Juknis (Tingkat digabung, kolom Skor dipecah
            | Individu/Beregu x Luring/Daring). TIDAK dipaginasi -- dataset per
            | kombinasi bidang+jenis+tahun itu kecil (puluhan baris), aman
            | ditampilkan sekaligus, dan paginasi 30-baris malah bisa motong
            | satu kelompok Tingkat jadi 2 halaman kalau dipaksa dipakai.
            |--------------------------------------------------------------------------
            */
            $semuaRubrik = $queryDasar
                ->orderByRaw("FIELD(tingkat, 'Internasional', 'Nasional', 'Provinsi', 'Kabupaten/Kota')")
                ->orderByRaw("FIELD(juara, 'Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Harapan 2', 'Harapan 3')")
                ->get();

            $rubrikTerkelompok = $semuaRubrik
                ->groupBy('bidang_prestasi')
                ->map(function ($rowsBidang) {
                    return [
                        'lomba' => $this->pivotRubrikLomba($rowsBidang->where('jenis_rubrik', 'Lomba')),
                        'lainnya' => $rowsBidang->where('jenis_rubrik', '!=', 'Lomba')->values(),
                    ];
                });
        }
        $daftarTahun = RubrikPenilaian::select('tahun_berlaku')
            ->distinct()
            ->orderByDesc('tahun_berlaku')
            ->pluck('tahun_berlaku');

        $breadcrumb = breadcrumb(['Rubrik Penilaian']);

        return view('rubrik-penilaian.index', [
            'sedangCari' => $sedangCari,
            'daftarRubrik' => $daftarRubrik,
            'rubrikTerkelompok' => $rubrikTerkelompok,
            'semuaRubrikUntukModal' => $semuaRubrik ?? ($daftarRubrik ? collect($daftarRubrik->items()) : collect()),
            'daftarTahun' => $daftarTahun,
            'bidangList' => self::BIDANG_LIST,
            'jenisRubrikList' => self::JENIS_RUBRIK_LIST,
            'tingkatList' => self::TINGKAT_LIST,
            'juaraList' => self::JUARA_LIST,
            'kategoriKegiatanList' => self::KATEGORI_KEGIATAN_LIST,
            'metodeList' => self::METODE_LIST,
            'penyelenggaraList' => self::PENYELENGGARA_LIST,
            'filterBidang' => $request->bidang_prestasi,
            'filterJenis' => $request->jenis_rubrik,
            'filterTahun' => $request->tahun_berlaku,
            'filterSearch' => $request->search,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | "PUTAR" RUBRIK JENIS "LOMBA" JADI FORMAT TABEL RESMI JUKNIS
    |--------------------------------------------------------------------------
    | Sama persis konsepnya dengan AsesorController::pivotRubrikLomba() --
    | menggabungkan beberapa baris (Individu/Beregu x Luring/Daring) jadi
    | satu baris tampilan per Tingkat x Juara, dipisah per Penyelenggara.
    |
    | BEDA dari versi Asesor: di sini setiap SEL skor juga membawa 'id' dan
    | 'idKosong' -- karena halaman ini perlu tombol Edit/Hapus per baris
    | database asli, bukan cuma tampil baca-saja seperti punya Asesor.
    |--------------------------------------------------------------------------
    */
    private function pivotRubrikLomba($rubrikLomba): array
    {
        if ($rubrikLomba->isEmpty()) {
            return [];
        }

        return $rubrikLomba
            ->groupBy('kategori_penyelenggara')
            ->map(function ($rows) {
                $adaMetode = $rows->contains(fn ($r) => $r->metode_pelaksanaan !== null);

                $perTingkatJuara = $rows
                    ->groupBy(fn ($r) => $r->tingkat . '||' . $r->juara)
                    ->map(function ($items) use ($adaMetode) {
                        $baris = [
                            'tingkat' => $items->first()->tingkat,
                            'juara' => $items->first()->juara,
                        ];

                        foreach ($items as $item) {
                            $kolom = $adaMetode
                                ? strtolower($item->kategori_kegiatan) . '_' . strtolower($item->metode_pelaksanaan)
                                : strtolower($item->kategori_kegiatan);

                            // Simpan objek RubrikPenilaian utuh (bukan cuma
                            // angka skor) -- Blade butuh ->id buat tombol
                            // Edit/Hapus per sel.
                            $baris[$kolom] = $item;
                        }

                        return (object) $baris;
                    })
                    ->values();

                $perTingkat = $perTingkatJuara
                    ->groupBy('tingkat')
                    ->map(fn ($baris, $tingkat) => [
                        'tingkat' => $tingkat,
                        'rowspan' => $baris->count(),
                        'baris' => $baris->values(),
                    ])
                    ->values();

                return [
                    'ada_metode' => $adaMetode,
                    'grup' => $perTingkat,
                ];
            })
            ->toArray();
    }

    public function store(Request $request)
    {
        $validated = $this->validasi($request);

        $rubrik = RubrikPenilaian::create($validated);

        ActivityLogger::log(
            event: 'create',
            description: 'Menambahkan rubrik penilaian: ' . $this->labelRubrik($rubrik),
            subject: $rubrik,
            properties: $validated
        );

        return back()->with('success', 'Rubrik penilaian berhasil ditambahkan.');
    }

    public function update(Request $request, RubrikPenilaian $rubrik_penilaian)
    {
        $validated = $this->validasi($request);

        $rubrik_penilaian->update($validated);

        ActivityLogger::log(
            event: 'update',
            description: 'Mengubah rubrik penilaian: ' . $this->labelRubrik($rubrik_penilaian),
            subject: $rubrik_penilaian,
            properties: $validated
        );

        return back()->with('success', 'Rubrik penilaian berhasil diperbarui.');
    }

    public function destroy(RubrikPenilaian $rubrik_penilaian)
    {
        $label = $this->labelRubrik($rubrik_penilaian);

        ActivityLogger::log(
            event: 'delete',
            description: 'Menghapus rubrik penilaian: ' . $label,
            subject: $rubrik_penilaian
        );

        $rubrik_penilaian->delete();

        return back()->with('success', 'Rubrik penilaian berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    | Kolom terstruktur (tingkat, juara, dst) & kolom fleksibel
    | (kriteria_khusus, nilai_min/max) SAMA-SAMA nullable di level validasi --
    | mana yang wajib diisi tergantung jenis_rubrik yang dipilih, itu diatur
    | di sisi tampilan (JS show/hide), bukan dipaksa di sini. Ini sengaja,
    | supaya satu tabel bisa menampung 4 jenis rubrik yang strukturnya beda.
    |--------------------------------------------------------------------------
    */
    private function validasi(Request $request): array
    {
        return $request->validate([
            'bidang_prestasi' => ['required', Rule::in(self::BIDANG_LIST)],
            'jenis_rubrik' => ['required', Rule::in(self::JENIS_RUBRIK_LIST)],
            'tingkat' => ['nullable', Rule::in(self::TINGKAT_LIST)],
            'juara' => ['nullable', Rule::in(self::JUARA_LIST)],
            'kategori_kegiatan' => ['nullable', Rule::in(self::KATEGORI_KEGIATAN_LIST)],
            'metode_pelaksanaan' => ['nullable', Rule::in(self::METODE_LIST)],
            'kategori_penyelenggara' => ['nullable', Rule::in(self::PENYELENGGARA_LIST)],
            'kriteria_khusus' => ['nullable', 'string', 'max:255'],
            'nilai_min' => ['nullable', 'numeric'],
            'nilai_max' => ['nullable', 'numeric', 'gte:nilai_min'],
            'skor' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'tahun_berlaku' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);
    }

    private function labelRubrik(RubrikPenilaian $rubrik): string
    {
        if ($rubrik->jenis_rubrik === 'Lomba') {
            return "{$rubrik->bidang_prestasi} - {$rubrik->tingkat} - {$rubrik->juara} - {$rubrik->kategori_kegiatan}";
        }

        return "{$rubrik->bidang_prestasi} - {$rubrik->jenis_rubrik} - " . ($rubrik->kriteria_khusus ?? '-');
    }
}