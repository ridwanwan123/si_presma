<?php

namespace App\Http\Controllers;

use App\Models\Madrasah;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MadrasahController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // =========================
        // ROLE MADRASAH → langsung profile sendiri
        // =========================
        if ($user->hasRole('Operator Madrasah')) {
            return redirect()->route('madrasah.show', $user->madrasah_id);
        }

        // =========================
        // ADMIN / PENGAWAS → list semua
        // =========================
        $query = Madrasah::query();

        if ($request->filled('jenjang_madrasah')) {
            $query->where('jenjang_madrasah', $request->jenjang_madrasah);
        }

        if ($request->filled('nama_madrasah')) {
            $query->where('nama_madrasah', $request->nama_madrasah);
        }

        if ($request->filled('kota')) {
            $query->where('kota', $request->kota);
        }

        $madrasahs = $query->latest()->get();

        $kotas = Madrasah::select('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');

        $breadcrumb = breadcrumb(['Madrasah']);

        return view('madrasah.index', compact(
            'madrasahs',
            'kotas',
            'breadcrumb'
        ));
    }

    public function show(Madrasah $madrasah)
    {
        $user = auth()->user();

        // Operator hanya boleh lihat miliknya
        if ($user->hasRole('Operator Madrasah')) {
            abort_unless($user->madrasah_id === $madrasah->id, 403);
        }

        return view('madrasah.profile', compact('madrasah'));
    }

    public function create()
    {
        $breadcrumb = breadcrumb([
            'Madrasah' => route('madrasah.index'),
            'Tambah Data'
        ]);

        return view('madrasah.form', [
            'mode' => 'create',
            'breadcrumb' => $breadcrumb,
            'madrasah' => new Madrasah(),
        ]);
    }

    private function decodeBase64($data)
    {
        if (str_contains($data, 'base64,')) {
            $data = explode('base64,', $data)[1];
        }

        return base64_decode(str_replace(' ', '+', $data));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jenjang_madrasah' => 'required|string|max:255',
            'status_madrasah' => 'nullable|string|max:255',

            'logo_cropped' => 'nullable|string',

            'nama_madrasah' => 'required|string|max:255',
            'npsn' => 'required|string|max:255|unique:madrasahs,npsn',
            'akreditasi' => 'nullable|string|max:10',

            'provinsi' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'alamat_sekolah' => 'nullable|string',

            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            'nama_kepala_madrasah' => 'required|string|max:255',
            'nip_kepala_madrasah' => 'nullable|digits:18',
            'no_telepon_kamad' => 'nullable|string|max:20',

            'foto_kamad_cropped' => 'nullable|string',
            'foto_katu_cropped' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            /*
            |-----------------------------------
            | LOGO
            |-----------------------------------
            */
            if (!empty($validatedData['logo_cropped'])) {

                $image = $this->decodeBase64($validatedData['logo_cropped']);

                $path = 'madrasah/logo/logo_' . time() . '.png';

                Storage::disk('public')->put($path, $image);

                $validatedData['logo'] = $path;
            }

            /*
            |-----------------------------------
            | FOTO KAMAD
            |-----------------------------------
            */
            if (!empty($validatedData['foto_kamad_cropped'])) {

                $image = $this->decodeBase64($validatedData['foto_kamad_cropped']);

                $path = 'madrasah/kamad/kamad_' . time() . '.png';

                Storage::disk('public')->put($path, $image);

                $validatedData['foto_kamad'] = $path;
            }

            /*
            |-----------------------------------
            | FOTO KTU
            |-----------------------------------
            */
            if (!empty($validatedData['foto_katu_cropped'])) {

                $image = $this->decodeBase64($validatedData['foto_katu_cropped']);

                $path = 'madrasah/katu/katu_' . time() . '.png';

                Storage::disk('public')->put($path, $image);

                $validatedData['foto_katu'] = $path;
            }

            $madrasah = Madrasah::create($validatedData);

            DB::commit();

            return redirect()
                ->route('madrasah.index')
                ->with('success', 'Data berhasil ditambahkan.');

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('STORE MADRASAH ERROR', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', $e->getMessage()); // 👈 penting biar keliatan error
        }
    }

    public function edit(Madrasah $madrasah)
    {
        $breadcrumb = breadcrumb([
            'Madrasah' => route('madrasah.index'),
            'Edit Data'
        ]);

        return view('madrasah.form', [
            'mode' => 'edit',
            'breadcrumb' => $breadcrumb,
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

            $oldData = $madrasah->toArray();

            /*
            |---------------------------------------
            | LOGO (kalau masih file upload biasa)
            |---------------------------------------
            */
            if ($request->filled('logo_cropped')) {

                $imageData = $request->logo_cropped;

                $image = str_replace('data:image/png;base64,', '', $imageData);
                $image = str_replace(' ', '+', $image);

                $fileName = 'logo_' . time() . '.png';
                $path = 'madrasah/logo/' . $fileName;

                Storage::disk('public')->put(
                    $path,
                    base64_decode($image)
                );

                // hapus lama
                if ($madrasah->logo) {
                    Storage::disk('public')->delete($madrasah->logo);
                }

                $validatedData['logo'] = $path;
            }

            /*
            |---------------------------------------
            | FOTO KAMAD (CROPPER BASE64)
            |---------------------------------------
            */
            if ($request->filled('foto_kamad_cropped')) {

                $imageData = $request->foto_kamad_cropped;

                $image = str_replace('data:image/png;base64,', '', $imageData);
                $image = str_replace(' ', '+', $image);

                $fileName = 'kamad_' . time() . '.png';
                $path = 'madrasah/kamad/' . $fileName;

                Storage::disk('public')->put(
                    $path,
                    base64_decode($image)
                );

                // hapus lama
                if ($madrasah->foto_kamad) {
                    Storage::disk('public')->delete($madrasah->foto_kamad);
                }

                $validatedData['foto_kamad'] = $path;
            }

            /*
            |---------------------------------------
            | FOTO KTU (MASIH FILE BIASA)
            |---------------------------------------
            */
            if ($request->filled('foto_katu_cropped')) {

                $imageData = $request->foto_katu_cropped;

                $image = str_replace('data:image/png;base64,', '', $imageData);
                $image = str_replace(' ', '+', $image);

                $fileName = 'katu_' . time() . '.png';
                $path = 'madrasah/katu/' . $fileName;

                Storage::disk('public')->put(
                    $path,
                    base64_decode($image)
                );

                // hapus lama
                if ($madrasah->foto_katu) {
                    Storage::disk('public')->delete($madrasah->foto_katu);
                }

                $validatedData['foto_katu'] = $path;
            }

            /*
            |---------------------------------------
            | UPDATE DATA
            |---------------------------------------
            */
            $madrasah->update($validatedData);
            $madrasah->refresh();

            $newData = $madrasah->toArray();

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

            Log::error('Gagal update madrasah', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Data gagal diperbarui.');
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
