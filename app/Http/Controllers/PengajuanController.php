<?php

namespace App\Http\Controllers;

use App\Models\PrestasiSiklus;
use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengajuanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $siklus = auth()->user()->madrasah->prestasiSiklusAktif();

        $ringkasan = PrestasiSiswa::visible()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN bidang_prestasi = 'Akademik' THEN 1 ELSE 0 END) as akademik,
                SUM(CASE WHEN bidang_prestasi = 'Non Akademik' THEN 1 ELSE 0 END) as non_akademik,
                SUM(CASE WHEN bidang_prestasi = 'Keagamaan' THEN 1 ELSE 0 END) as keagamaan,
                SUM(CASE WHEN bidang_prestasi = 'GTK' THEN 1 ELSE 0 END) as gtk,
                SUM(CASE WHEN bidang_prestasi = 'Lembaga' THEN 1 ELSE 0 END) as lembaga
            ")
            ->first();

        $summary = (object) [
            'total'         => $ringkasan->total ?? 0,
            'akademik'      => $ringkasan->akademik ?? 0,
            'non_akademik'  => $ringkasan->non_akademik ?? 0,
            'keagamaan'     => $ringkasan->keagamaan ?? 0,
            'gtk'           => $ringkasan->gtk ?? 0,
            'lembaga'       => $ringkasan->lembaga ?? 0,
        ];

        return view('prestasi.pengajuan', compact('siklus', 'summary'));
    }

    /*
    |--------------------------------------------------------------------------
    | SUBMIT (KIRIM PRESTASI)
    |--------------------------------------------------------------------------
    */
    public function submit(Request $request)
    {
        $siklus = auth()->user()->madrasah->prestasiSiklusAktif();

        /*
        |--------------------------------------------------------------------------
        | Validasi 1: Status siklus masih OPEN
        |--------------------------------------------------------------------------
        */
        if (!$siklus->canInput()) {
            return redirect()
                ->route('pengajuan.index')
                ->with('error', 'Pengajuan tidak dapat dikirim karena status siklus bukan OPEN.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi 2: Minimal memiliki 1 data prestasi
        |--------------------------------------------------------------------------
        */
        $totalPrestasi = PrestasiSiswa::visible()->count();

        if ($totalPrestasi < 1) {
            return redirect()
                ->route('pengajuan.index')
                ->with('error', 'Minimal harus ada 1 data prestasi sebelum pengajuan dapat dikirim.');
        }

        try {
            DB::beginTransaction();

            $siklus->update([
                'status'       => PrestasiSiklus::SUBMITTED,
                'submitted_at' => now(),
                'submitted_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('pengajuan.index')
                ->with('success', 'Prestasi berhasil dikirim.');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Gagal mengirim pengajuan prestasi', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'madrasah_id' => auth()->user()->madrasah_id,
            ]);

            return redirect()
                ->route('pengajuan.index')
                ->with('error', 'Terjadi kesalahan saat mengirim pengajuan prestasi.');
        }
    }
}