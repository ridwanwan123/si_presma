<?php

namespace App\Http\Controllers;

use App\Models\Madrasah;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MadrasahController extends Controller
{
    public function index(Request $request)
    {
        $query = Madrasah::query();

        if ($request->filled('jenjang_madrasah')) {
            $query->where(
                'jenjang_madrasah',
                $request->jenjang_madrasah
            );

        }

        if ($request->filled('nama_madrasah')) {
            $query->where(
                'nama_madrasah',
                $request->nama_madrasah
            );
        }


        if ($request->filled('kota')) {
            $query->where(
                'kota',
                $request->kota
            );
        }


        $madrasahs = $query->get();

        // untuk dropdown filter
        $kotas = Madrasah::select('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');


        return view('madrasah.index', compact(
            'madrasahs',
            'kotas'
        ));
    }

    public function create()
    {
        return view('madrasah.form', [
            'mode' => 'create',
            'madrasah' => new Madrasah(),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jenjang_madrasah' => 'required|string|max:255',
            'nama_madrasah' => 'required|string|max:255',
            'npsn' => 'required|string|max:255|unique:madrasahs,npsn',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'akreditasi' => 'required|string|max:255',
            'alamat_sekolah' => 'required|string',
            'nama_kepala_madrasah' => 'required|string|max:255',
            'nip_kepala_madrasah' => 'nullable|digits:18',
            'nama_kepala_urusan_tata_usaha' => 'nullable|string|max:255',
            'nip_kepala_urusan_tata_usaha' => 'nullable|digits:18',
        ]);

        try {
            DB::beginTransaction();
            /*
            |--------------------------------------------------------------------------
            | Simpan Data Madrasah
            |--------------------------------------------------------------------------
            */

            $madrasah = Madrasah::create($validatedData);

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            ActivityLogger::log(
                event: 'create',
                description: 'Insert Data Madrasah',
                subject: $madrasah,
                properties: $validatedData
            );
            DB::commit();

            return redirect()
                ->route('madrasah.index')
                ->with('success', 'Data madrasah berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menambahkan data madrasah', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $validatedData,
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Data madrasah gagal ditambahkan.');
        }
    }

    public function edit(Madrasah $madrasah)
    {
        return view('madrasah.form', [
            'mode' => 'edit',
            'madrasah' => $madrasah,
        ]);
    }

    public function update(Request $request, Madrasah $madrasah)
    {
        $validatedData = $request->validate([
            'jenjang_madrasah' => 'required|string|max:255',
            'nama_madrasah' => 'required|string|max:255',
            'npsn' => 'required|string|max:255|unique:madrasahs,npsn,' . $madrasah->id,
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'akreditasi' => 'nullable|string|max:255',
            'alamat_sekolah' => 'required|string',
            'nama_kepala_madrasah' => 'required|string|max:255',
            'nip_kepala_madrasah' => 'nullable|digits:18',
            'nama_kepala_urusan_tata_usaha' => 'nullable|string|max:255',
            'nip_kepala_urusan_tata_usaha' => 'nullable|digits:18',
        ]);

        try {
            DB::beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Simpan data lama sebelum update
            |--------------------------------------------------------------------------
            */

            $oldData = $madrasah->only([
                'jenjang_madrasah',
                'nama_madrasah',
                'npsn',
                'kota',
                'provinsi',
                'akreditasi',
                'alamat_sekolah',
                'nama_kepala_madrasah',
                'nip_kepala_madrasah',
                'nama_kepala_urusan_tata_usaha',
                'nip_kepala_urusan_tata_usaha',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update data
            |--------------------------------------------------------------------------
            */

            $madrasah->update($validatedData);
            $madrasah->refresh();

            /*
            |--------------------------------------------------------------------------
            | Data baru setelah update
            |--------------------------------------------------------------------------
            */

            $newData = $madrasah->only([
                'jenjang_madrasah',
                'nama_madrasah',
                'npsn',
                'kota',
                'provinsi',
                'akreditasi',
                'alamat_sekolah',
                'nama_kepala_madrasah',
                'nip_kepala_madrasah',
                'nama_kepala_urusan_tata_usaha',
                'nip_kepala_urusan_tata_usaha',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            ActivityLogger::log(
                event: 'update',
                description: 'Mengubah data madrasah',
                subject: $madrasah,
                properties: [
                    'old' => $oldData,
                    'new' => $newData,
                ]
            );

            DB::commit();

            return redirect()
                ->route('madrasah.index')
                ->with('success', 'Data madrasah berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal memperbarui data madrasah', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'madrasah_id' => $madrasah->id,
                'data' => $validatedData,
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Data madrasah gagal diperbarui.');
        }
    }

    public function destroy(Madrasah $madrasah)
    {
        try {
            DB::beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Backup data sebelum delete
            |--------------------------------------------------------------------------
            */

            $deletedData = $madrasah->only([
                'id',
                'jenjang_madrasah',
                'nama_madrasah',
                'npsn',
                'kota',
                'provinsi',
                'akreditasi',
                'alamat_sekolah',
                'nama_kepala_madrasah',
                'nip_kepala_madrasah',
                'nama_kepala_urusan_tata_usaha',
                'nip_kepala_urusan_tata_usaha',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Delete data
            |--------------------------------------------------------------------------
            */

            $madrasah->delete();

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            ActivityLogger::log(
                event: 'delete',
                description: 'Menghapus data madrasah',
                subject: $madrasah,
                properties: [
                    'deleted_data' => $deletedData,
                ]
            );

            DB::commit();

            return redirect()
                ->route('madrasah.index')
                ->with('success', 'Data madrasah berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal menghapus data madrasah', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'madrasah_id' => $madrasah->id,
            ]);

            return redirect()
                ->route('madrasah.index')
                ->with('error', 'Data madrasah gagal dihapus.');
        }
    }

}
