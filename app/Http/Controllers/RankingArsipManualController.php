<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Madrasah;
use App\Models\RankingArsip;
use App\Models\RankingArsipDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankingArsipManualController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INPUT MANUAL DATA ARSIP HISTORIS
    |--------------------------------------------------------------------------
    | Untuk memasukkan data JMA tahun-tahun lama (mis. 2022-2025) yang cuma
    | ada di dokumen PDF/cetak -- data itu tidak pernah ada di sistem, jadi
    | tombol "Arsipkan Ranking" (yang menghitung dari data live) tidak bisa
    | dipakai. Di sini admin mengetik ulang datanya lewat form.
    |
    | Peringkat TIDAK diinput manual -- selalu dihitung ulang otomatis dari
    | total_nilai_akhir (urut terbesar) setiap kali ada baris ditambah/
    | diubah/dihapus, supaya tidak pernah ada peringkat ganda/loncat.
    */

    /*
    |--------------------------------------------------------------------------
    | FORM BUAT ARSIP PERIODE BARU (MANUAL)
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $breadcrumb = breadcrumb([
            'Arsip Ranking' => route('ranking-arsip.index'),
            'Input Manual'
        ]);

        return view('ranking-arsip.manual-create', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode' => ['required', 'integer', 'min:2000', 'max:2100', 'unique:ranking_arsips,periode'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'periode.unique' => 'Periode ini sudah punya arsip. Buka arsipnya langsung untuk menambah/mengubah data.',
        ]);

        $arsip = RankingArsip::create([
            'periode' => $validated['periode'],
            'diarsipkan_oleh' => auth()->id(),
            'diarsipkan_pada' => now(),
            'catatan' => $validated['catatan'] ?? 'Input manual dari dokumen arsip',
        ]);

        ActivityLogger::log(
            event: 'create',
            description: 'Membuat arsip ranking manual periode ' . $arsip->periode,
            subject: $arsip
        );

        return redirect()
            ->route('ranking-arsip.kelola', $arsip->id)
            ->with('success', 'Arsip periode ' . $arsip->periode . ' dibuat. Silakan input data madrasah satu per satu.');
    }

    /*
    |--------------------------------------------------------------------------
    | HALAMAN KELOLA ISI ARSIP (tambah/edit/hapus baris madrasah)
    |--------------------------------------------------------------------------
    | Dipakai baik untuk arsip manual maupun arsip hasil tombol "Arsipkan" --
    | keduanya struktur datanya sama persis.
    */
    public function kelola(RankingArsip $ranking_arsip)
    {
        $detail = $ranking_arsip->details()->orderBy('peringkat')->get();

        $daftarMadrasah = Madrasah::orderBy('nama_madrasah')
            ->get(['id', 'nama_madrasah', 'npsn', 'jenjang_madrasah', 'kota']);

        $breadcrumb = breadcrumb([
            'Arsip Ranking' => route('ranking-arsip.index'),
            'Kelola Periode ' . $ranking_arsip->periode
        ]);

        return view('ranking-arsip.kelola', compact('ranking_arsip', 'detail', 'daftarMadrasah', 'breadcrumb'));
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH SATU BARIS MADRASAH
    |--------------------------------------------------------------------------
    */
    public function storeDetail(Request $request, RankingArsip $ranking_arsip)
    {
        $validated = $this->validasiDetail($request);

        // Cegah satu madrasah muncul dua kali dalam arsip yang sama
        // (dicek via nama, karena data historis bisa saja madrasahnya
        // sudah tidak terdaftar di master data sekarang).
        $sudahAda = $ranking_arsip->details()
            ->where('nama_madrasah', $validated['nama_madrasah'])
            ->exists();

        if ($sudahAda) {
            return back()
                ->withInput()
                ->with('error', 'Madrasah "' . $validated['nama_madrasah'] . '" sudah ada di arsip periode ini. Edit baris yang sudah ada.');
        }

        DB::transaction(function () use ($ranking_arsip, $validated) {

            RankingArsipDetail::create(array_merge(
                $this->hitungBarisDetail($validated),
                [
                    'ranking_arsip_id' => $ranking_arsip->id,
                    'peringkat'        => 0, // sementara -- langsung dihitung ulang di bawah
                ]
            ));

            $this->hitungUlangPeringkat($ranking_arsip);
        });

        return back()->with('success', 'Data "' . $validated['nama_madrasah'] . '" berhasil ditambahkan, peringkat diperbarui otomatis.');
    }

    public function updateDetail(Request $request, RankingArsip $ranking_arsip, RankingArsipDetail $detail)
    {
        abort_if($detail->ranking_arsip_id !== $ranking_arsip->id, 404);

        $validated = $this->validasiDetail($request);

        DB::transaction(function () use ($ranking_arsip, $detail, $validated) {

            $detail->update($this->hitungBarisDetail($validated));

            $this->hitungUlangPeringkat($ranking_arsip);
        });

        return back()->with('success', 'Data "' . $validated['nama_madrasah'] . '" berhasil diperbarui, peringkat dihitung ulang.');
    }

    public function destroyDetail(RankingArsip $ranking_arsip, RankingArsipDetail $detail)
    {
        abort_if($detail->ranking_arsip_id !== $ranking_arsip->id, 404);

        $nama = $detail->nama_madrasah;

        DB::transaction(function () use ($ranking_arsip, $detail) {
            $detail->delete();
            $this->hitungUlangPeringkat($ranking_arsip);
        });

        return back()->with('success', 'Data "' . $nama . '" dihapus, peringkat dihitung ulang.');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */
    private function validasiDetail(Request $request): array
    {
        return $request->validate([
            'madrasah_id'            => ['nullable', 'exists:madrasahs,id'],
            'nama_madrasah'          => ['required', 'string', 'max:255'],
            'npsn'                   => ['nullable', 'string', 'max:50'],
            'jenjang_madrasah'       => ['nullable', 'string', 'max:50'],
            'kota'                   => ['nullable', 'string', 'max:100'],
            'nilai_akademik'         => ['required', 'numeric', 'min:0'],
            'nilai_non_akademik'     => ['required', 'numeric', 'min:0'],
            'nilai_keagamaan'        => ['required', 'numeric', 'min:0'],
            'nilai_gtk'              => ['required', 'numeric', 'min:0'],
            'nilai_lembaga'          => ['required', 'numeric', 'min:0'],
            'potongan_aduan'         => ['nullable', 'numeric', 'min:0'],
            'potongan_keterlambatan' => ['nullable', 'numeric', 'min:0'],
            'jumlah_prestasi_dinilai' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    /**
     * total_nilai_asesor & total_nilai_akhir TIDAK diinput manual --
     * dihitung dari komponen yang diketik, supaya tidak mungkin ada
     * salah jumlah antara rincian dan totalnya.
     */
    private function hitungBarisDetail(array $validated): array
    {
        $totalAsesor = round(
            $validated['nilai_akademik']
            + $validated['nilai_non_akademik']
            + $validated['nilai_keagamaan']
            + $validated['nilai_gtk']
            + $validated['nilai_lembaga'],
            2
        );

        $potonganAduan = round($validated['potongan_aduan'] ?? 0, 2);
        $potonganTelat = round($validated['potongan_keterlambatan'] ?? 0, 2);

        $totalAkhir = round(max(0, $totalAsesor - $potonganAduan - $potonganTelat), 2);

        return [
            'madrasah_id'             => $validated['madrasah_id'] ?? null,
            'nama_madrasah'           => $validated['nama_madrasah'],
            'npsn'                    => $validated['npsn'] ?? null,
            'jenjang_madrasah'        => $validated['jenjang_madrasah'] ?? null,
            'kota'                    => $validated['kota'] ?? null,
            'nilai_akademik'          => $validated['nilai_akademik'],
            'nilai_non_akademik'      => $validated['nilai_non_akademik'],
            'nilai_keagamaan'         => $validated['nilai_keagamaan'],
            'nilai_gtk'               => $validated['nilai_gtk'],
            'nilai_lembaga'           => $validated['nilai_lembaga'],
            'total_nilai_asesor'      => $totalAsesor,
            'potongan_aduan'          => $potonganAduan,
            'potongan_keterlambatan'  => $potonganTelat,
            'total_nilai_akhir'       => $totalAkhir,
            'jumlah_prestasi_dinilai' => $validated['jumlah_prestasi_dinilai'] ?? 0,
        ];
    }

    /**
     * Urutkan ulang SEMUA baris arsip ini berdasar total_nilai_akhir
     * terbesar, tulis ulang kolom peringkat 1..N.
     */
    private function hitungUlangPeringkat(RankingArsip $arsip): void
    {
        $urutan = $arsip->details()
            ->orderByDesc('total_nilai_akhir')
            ->pluck('id');

        foreach ($urutan as $index => $detailId) {
            RankingArsipDetail::where('id', $detailId)->update(['peringkat' => $index + 1]);
        }
    }
}