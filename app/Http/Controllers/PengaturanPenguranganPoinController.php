<?php

namespace App\Http\Controllers;

use App\Models\PengaturanPenguranganPoin;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class PengaturanPenguranganPoinController extends Controller
{
    public function index()
    {
        $pengaturanAduan = PengaturanPenguranganPoin::where('kategori', 'aduan_masyarakat')
            ->orderBy('id')
            ->get();

        $pengaturanKeterlambatan = PengaturanPenguranganPoin::where('kategori', 'keterlambatan')
            ->orderBy('id')
            ->get();

        $breadcrumb = breadcrumb([
            'Pengurangan Poin' => route('pengurangan-poin.pengaturan'),
            'Pengaturan Nilai'
        ]);

        return view('pengurangan-poin.pengaturan', compact(
            'pengaturanAduan',
            'pengaturanKeterlambatan',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE — satu form, banyak baris nilai sekaligus (bukan hardcode di
    | kode, semua nilai persen/poin diinput lewat sini).
    |--------------------------------------------------------------------------
    */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($validated['nilai'] as $id => $nilai) {

            $pengaturan = PengaturanPenguranganPoin::find($id);

            if (!$pengaturan) {
                continue;
            }

            $pengaturan->update(['nilai' => $nilai]);
        }

        ActivityLogger::log(
            event: 'update',
            description: 'Mengubah pengaturan nilai pengurangan poin',
            properties: $validated['nilai']
        );

        return redirect()
            ->route('pengurangan-poin.pengaturan')
            ->with('success', 'Pengaturan nilai potongan berhasil disimpan.');
    }
}