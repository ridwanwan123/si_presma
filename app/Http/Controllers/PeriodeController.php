<?php

namespace App\Http\Controllers;

use App\Models\PeriodeAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\ActivityLogger;

class PeriodeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — riwayat periode + form aktifkan/buat periode baru
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $daftarPeriode = PeriodeAktif::with('diaktifkanOleh')
            ->orderByDesc('periode')
            ->get();

        $periodeAktif = PeriodeAktif::aktif();

        $breadcrumb = breadcrumb([
            'Kelola Periode'
        ]);

        return view('periode.index', compact(
            'daftarPeriode',
            'periodeAktif',
            'breadcrumb'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | AKTIFKAN — pindah periode aktif (bisa ke periode yang sudah ada di
    | riwayat, atau bikin baris periode baru kalau belum pernah ada).
    |--------------------------------------------------------------------------
    | PENTING: ini cuma memindahkan "penanda" periode aktif secara global.
    | Data prestasi_siklus per madrasah untuk periode baru tetap dibuat
    | belakangan secara lazy lewat Madrasah::prestasiSiklusAktif() begitu
    | madrasah yang bersangkutan pertama kali membuka halaman -- bukan
    | dibuatkan massal di sini, supaya tidak membuat ribuan baris kosong
    | untuk madrasah yang belum tentu aktif tahun itu.
    */
    public function aktifkan(Request $request)
    {
        $validated = $request->validate([
            'periode'    => 'required|integer|digits:4|min:2020|max:2100',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Data lama sebelum diubah, buat activity log
            $periodeSebelumnya = PeriodeAktif::where('is_active', true)->value('periode');

            // Nonaktifkan seluruh periode lain, cuma boleh satu yang aktif
            PeriodeAktif::where('is_active', true)->update(['is_active' => false]);

            $periodeAktif = PeriodeAktif::updateOrCreate(
                [
                    'periode' => $validated['periode'],
                ],
                [
                    'is_active'       => true,
                    'diaktifkan_oleh' => auth()->id(),
                    'diaktifkan_pada' => now(),
                    'keterangan'      => $validated['keterangan'] ?? null,
                ]
            );

            ActivityLogger::log(
                event: 'switch_periode',
                description: 'Administrator mengaktifkan periode ' . $validated['periode'],
                subject: $periodeAktif,
                properties: [
                    'periode_sebelumnya' => $periodeSebelumnya,
                    'periode_baru'       => $validated['periode'],
                    'keterangan'         => $validated['keterangan'] ?? null,
                ]
            );

            DB::commit();

            return redirect()
                ->route('periode.index')
                ->with('success', 'Periode ' . $validated['periode'] . ' berhasil diaktifkan.');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Gagal mengaktifkan periode', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'periode' => $validated['periode'] ?? null,
            ]);

            return redirect()
                ->route('periode.index')
                ->with('error', 'Terjadi kesalahan saat mengaktifkan periode.');
        }
    }
}