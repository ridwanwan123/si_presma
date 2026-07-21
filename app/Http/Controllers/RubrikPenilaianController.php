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
        $query = RubrikPenilaian::query()
            ->when($request->filled('bidang_prestasi'), fn ($q) => $q->where('bidang_prestasi', $request->bidang_prestasi))
            ->when($request->filled('jenis_rubrik'), fn ($q) => $q->where('jenis_rubrik', $request->jenis_rubrik))
            ->when($request->filled('tahun_berlaku'), fn ($q) => $q->where('tahun_berlaku', $request->tahun_berlaku))
            ->when($request->filled('search'), function ($q) use ($request) {
                $keyword = $request->search;
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('kriteria_khusus', 'like', "%{$keyword}%")
                        ->orWhere('juara', 'like', "%{$keyword}%")
                        ->orWhere('tingkat', 'like', "%{$keyword}%")
                        ->orWhere('keterangan', 'like', "%{$keyword}%");
                });
            });

        $daftarRubrik = $query
            ->orderBy('bidang_prestasi')
            ->orderBy('jenis_rubrik')
            ->orderByDesc('tahun_berlaku')
            ->orderBy('tingkat')
            ->paginate(30)
            ->withQueryString();

        $daftarTahun = RubrikPenilaian::select('tahun_berlaku')
            ->distinct()
            ->orderByDesc('tahun_berlaku')
            ->pluck('tahun_berlaku');

        $breadcrumb = breadcrumb(['Rubrik Penilaian']);

        return view('rubrik-penilaian.index', [
            'daftarRubrik' => $daftarRubrik,
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