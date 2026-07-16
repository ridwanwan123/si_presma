<?php

namespace App\Http\Controllers;

use App\Models\AduanMasyarakat;
use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AduanMasyarakatController extends Controller
{
    public const TINGKAT_ADUAN = [
        'Pusat',
        'Badan Pemeriksa Keuangan',
        'Ombudsman',
        'Inspektorat Jenderal',
        'Kantor Wilayah',
        'Kota/Kabupaten',
    ];

    public function index(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();

        $daftarPeriode = AduanMasyarakat::select('periode')->distinct()->pluck('periode');

        if (!$daftarPeriode->contains($periode)) {
            $daftarPeriode->push($periode);
        }

        $daftarPeriode = $daftarPeriode->sortDesc()->values();

        $daftarAduan = AduanMasyarakat::with('madrasah')
            ->where('periode', $periode)
            ->orderByDesc('tanggal_aduan')
            ->paginate(20)
            ->withQueryString();

        $daftarMadrasah = Madrasah::orderBy('nama_madrasah')->get(['id', 'nama_madrasah']);

        $tingkatAduanList = self::TINGKAT_ADUAN;

        $breadcrumb = breadcrumb([
            'Pengurangan Poin' => route('pengurangan-poin.pengaturan'),
            'Aduan Masyarakat'
        ]);

        return view('pengurangan-poin.aduan', compact(
            'daftarAduan',
            'daftarMadrasah',
            'periode',
            'daftarPeriode',
            'tingkatAduanList',
            'breadcrumb'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'madrasah_id' => ['required', 'exists:madrasahs,id'],
            'periode' => ['required', 'integer'],
            'tingkat_aduan' => ['required', Rule::in(self::TINGKAT_ADUAN)],
            'permasalahan' => ['required', 'string', 'max:255'],
            'jumlah_tindak_lanjut' => ['required', 'integer', 'min:1'],
            'tanggal_aduan' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
        ]);

        $validated['created_by'] = auth()->id();

        $aduan = AduanMasyarakat::create($validated);

        ActivityLogger::log(
            event: 'create',
            description: 'Menambahkan catatan aduan masyarakat untuk madrasah ' . $aduan->madrasah->nama_madrasah,
            subject: $aduan,
            properties: $validated
        );

        return redirect()
            ->route('aduan-masyarakat.index', ['periode' => $validated['periode']])
            ->with('success', 'Data aduan masyarakat berhasil ditambahkan.');
    }

    public function update(Request $request, AduanMasyarakat $aduan_masyarakat)
    {
        $validated = $request->validate([
            'madrasah_id' => ['required', 'exists:madrasahs,id'],
            'periode' => ['required', 'integer'],
            'tingkat_aduan' => ['required', Rule::in(self::TINGKAT_ADUAN)],
            'permasalahan' => ['required', 'string', 'max:255'],
            'jumlah_tindak_lanjut' => ['required', 'integer', 'min:1'],
            'tanggal_aduan' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
        ]);

        $aduan_masyarakat->update($validated);

        ActivityLogger::log(
            event: 'update',
            description: 'Mengubah catatan aduan masyarakat',
            subject: $aduan_masyarakat,
            properties: $validated
        );

        return redirect()
            ->route('aduan-masyarakat.index', ['periode' => $validated['periode']])
            ->with('success', 'Data aduan masyarakat berhasil diperbarui.');
    }

    public function destroy(AduanMasyarakat $aduan_masyarakat)
    {
        $periode = $aduan_masyarakat->periode;

        ActivityLogger::log(
            event: 'delete',
            description: 'Menghapus catatan aduan masyarakat',
            subject: $aduan_masyarakat,
            properties: $aduan_masyarakat->toArray()
        );

        $aduan_masyarakat->delete();

        return redirect()
            ->route('aduan-masyarakat.index', ['periode' => $periode])
            ->with('success', 'Data aduan masyarakat berhasil dihapus.');
    }
}