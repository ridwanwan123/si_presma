<?php

namespace App\Http\Controllers;

use App\Models\KeterlambatanBerkas;
use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class KeterlambatanBerkasController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();

        $daftarPeriode = KeterlambatanBerkas::select('periode')->distinct()->pluck('periode');

        if (!$daftarPeriode->contains($periode)) {
            $daftarPeriode->push($periode);
        }

        $daftarPeriode = $daftarPeriode->sortDesc()->values();

        $daftarKeterlambatan = KeterlambatanBerkas::with('madrasah')
            ->where('periode', $periode)
            ->orderByDesc('jumlah_hari_terlambat')
            ->paginate(20)
            ->withQueryString();

        $daftarMadrasah = Madrasah::orderBy('nama_madrasah')->get(['id', 'nama_madrasah']);

        $breadcrumb = breadcrumb([
            'Pengurangan Poin' => route('pengurangan-poin.pengaturan'),
            'Keterlambatan Berkas'
        ]);

        return view('pengurangan-poin.keterlambatan', compact(
            'daftarKeterlambatan',
            'daftarMadrasah',
            'periode',
            'daftarPeriode',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — satu madrasah cuma boleh punya SATU catatan per periode.
    | updateOrCreate() otomatis menangani "belum ada -> INSERT, sudah ada
    | -> UPDATE", konsisten dengan unique constraint di migration.
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'madrasah_id' => ['required', 'exists:madrasahs,id'],
            'periode' => ['required', 'integer'],
            'jumlah_hari_terlambat' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $keterlambatan = KeterlambatanBerkas::updateOrCreate(
            [
                'madrasah_id' => $validated['madrasah_id'],
                'periode' => $validated['periode'],
            ],
            [
                'jumlah_hari_terlambat' => $validated['jumlah_hari_terlambat'],
                'keterangan' => $validated['keterangan'] ?? null,
                'created_by' => auth()->id(),
            ]
        );

        ActivityLogger::log(
            event: 'create',
            description: 'Mencatat keterlambatan pengumpulan berkas untuk madrasah ' . $keterlambatan->madrasah->nama_madrasah,
            subject: $keterlambatan,
            properties: $validated
        );

        return redirect()
            ->route('keterlambatan-berkas.index', ['periode' => $validated['periode']])
            ->with('success', 'Data keterlambatan berhasil disimpan.');
    }

    public function destroy(KeterlambatanBerkas $keterlambatan_berkas)
    {
        $periode = $keterlambatan_berkas->periode;

        ActivityLogger::log(
            event: 'delete',
            description: 'Menghapus catatan keterlambatan berkas',
            subject: $keterlambatan_berkas,
            properties: $keterlambatan_berkas->toArray()
        );

        $keterlambatan_berkas->delete();

        return redirect()
            ->route('keterlambatan-berkas.index', ['periode' => $periode])
            ->with('success', 'Data keterlambatan berhasil dihapus.');
    }
}