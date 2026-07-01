<?php

namespace App\Http\Controllers;

use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PrestasiSiswaImport;
use App\Exports\PrestasiTemplateExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\ActivityLogger;
use Yajra\DataTables\Facades\DataTables;

class PrestasiController extends Controller
{
    // UNTUK TAMPILAN CARD
    // public function index($jenis)
    // {
    //     $mapping = [
    //         'akademik' => 'Akademik',
    //         'non-akademik' => 'Non Akademik',
    //         'keagamaan' => 'Keagamaan',
    //         'gtk' => 'GTK',
    //         'lembaga' => 'Lembaga',
    //     ];

    //     $query = PrestasiSiswa::visible()
    //         ->where(
    //             'bidang_prestasi',
    //             $mapping[$jenis]
    //         );

    //     $user = auth()->user();

    //     if ($user->isOperator()) {

    //         $query->where(
    //             'madrasah_id',
    //             $user->madrasah_id
    //         );

    //     }

    //     $prestasi = (clone $query)
    //         ->latest()
    //         ->paginate(10);

    //     $summary = (clone $query)
    //         ->selectRaw("
    //             COUNT(*) as total,
    //             SUM(status_verifikasi = 'verified') as verified,
    //             SUM(status_verifikasi = 'pending') as pending,
    //             SUM(status_verifikasi = 'rejected') as rejected
    //         ")
    //         ->first();

    //     $breadcrumb = breadcrumb([
    //         'Prestasi ' => route('prestasi.index', $jenis),
    //         $mapping[$jenis]
    //     ]);

    //     return view(
    //         'prestasi.index',
    //         compact(
    //             'jenis',
    //             'prestasi',
    //             'summary',
    //             'breadcrumb'
    //         )
    //     );
    // }
    
    // public function data($jenis)
    // {
    //     return response()->json([
    //         'data' => PrestasiSiswa::where(
    //             'bidang_prestasi',
    //             ucfirst($jenis)
    //         )->get()
    //     ]);
    // }

    /*
    |--------------------------------------------------------------------------
    | INDEX & DATA
    |--------------------------------------------------------------------------
    */
    public function index($jenis)
    {
        $mapping = [
            'akademik' => 'Akademik',
            'non-akademik' => 'Non Akademik',
            'keagamaan' => 'Keagamaan',
            'gtk' => 'GTK',
            'lembaga' => 'Lembaga',
        ];

        $query = PrestasiSiswa::visible()
            ->where(
                'bidang_prestasi',
                $mapping[$jenis]
            );

        $summary = (clone $query)
            ->selectRaw("
                COUNT(*) as total,
                SUM(status_verifikasi = 'verified') as verified,
                SUM(status_verifikasi = 'pending') as pending,
                SUM(status_verifikasi = 'rejected') as rejected
            ")
            ->first();

        $breadcrumb = breadcrumb([
            'Prestasi' => route('prestasi.index', $jenis),
            $mapping[$jenis]
        ]);

        return view(
            'prestasi.index',
            compact(
                'jenis',
                'summary',
                'breadcrumb'
            )
        );
    }
    public function data($jenis)
    {
        $mapping = [
            'akademik'     => 'Akademik',
            'non-akademik' => 'Non Akademik',
            'keagamaan'    => 'Keagamaan',
            'gtk'          => 'GTK',
            'lembaga'      => 'Lembaga',
        ];

        $query = PrestasiSiswa::visible()
            ->where('bidang_prestasi', $mapping[$jenis])
            ->latest();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('waktu_kegiatan', function($item){
                return optional($item->waktu_kegiatan)
                    ->format('d M Y');
            })
            ->editColumn('skor_luring', function($item){
                return $item->skor_luring !== null
                    ? number_format($item->skor_luring,0,',','.')
                    : null;
            })
            ->editColumn('skor_daring', function($item){
                return $item->skor_daring !== null
                    ? number_format($item->skor_daring,0,',','.')
                    : null;
            })
            ->make(true);
    }

    /*
    |--------------------------------------------------------------------------
    | FITUR IMPORT
    |--------------------------------------------------------------------------
    */
    public function template($jenis)
    {
        return Excel::download(
            new PrestasiTemplateExport,
            'template-prestasi-' . $jenis . '.xlsx'
        );
    }

    public function import($jenis)
    {
        $mapping = [
            'akademik' => 'Akademik',
            'non-akademik' => 'Non Akademik',
            'keagamaan' => 'Keagamaan',
            'gtk' => 'GTK',
            'lembaga' => 'Lembaga',
        ];

        $breadcrumb = breadcrumb([
            'Prestasi ' => route('prestasi.index', $jenis),
            $mapping[$jenis] => route('prestasi.index', $jenis),
            'Import Prestasi '.$mapping[$jenis]
        ]);

        return view('prestasi.import', compact('jenis', 'breadcrumb'));
    }

    public function upload(Request $request, $jenis)
    {
        try {

            $request->validate([
                'file' => 'required|mimes:xlsx,xls'
            ]);

            $import = new PrestasiSiswaImport();

            Excel::import($import, $request->file('file'));

            return response()->json([
                'message' => 'success',
                'data' => $import->rows
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);

        }
    }

    private function normalizeKey($value)
    {
        $value = strtolower(trim((string) $value));

        // Hilangkan semua karakter selain huruf dan angka
        return preg_replace('/[^a-z0-9]/', '', $value);
    }

    public function checking_import_prestasi(Request $request, $jenis)
    {
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $mapping = [
            'akademik' => 'Akademik',
            'non-akademik' => 'Non Akademik',
            'keagamaan' => 'Keagamaan',
            'gtk' => 'GTK',
            'lembaga' => 'Lembaga',
        ];

        $normalizedMapping = collect($mapping)
            ->mapWithKeys(function ($value) {
                return [$this->normalizeKey($value) => $value];
            })
            ->toArray();

        $madrasah_id = auth()->user()->madrasah_id;
        $submitter = auth()->user()->nama;
        $bidang_prestasi = $mapping[$jenis];
        $periode = now()->year;

        $requiredFields = [
            'bidang_prestasi',
            'nama_kegiatan',
            'tingkat',
            'kategori_kegiatan',
            'juara',
            'lembaga_penyelenggara',
            'kategori_penyelenggara',
            'waktu_kegiatan',
            'skor_luring',
            'skor_daring',
            'link_drive_bukti',
        ];

        $validTingkat = [
            'kabupatenkota' => 'Kabupaten/Kota',
            'provinsi' => 'Provinsi',
            'nasional' => 'Nasional',
            'internasional' => 'Internasional',
        ];

        $validKategoriKegiatan = [
            'individu' => 'Individu',
            'beregu' => 'Beregu',
        ];

        $errors = [];
        $result = [];

        try {

            $import = new PrestasiSiswaImport();

            Excel::import($import, $request->file('file_import'));

            $data = $import->rows;

            foreach ($data as $index => $row) {

                // Rapikan seluruh kolom string
                foreach ($row as $key => $value) {
                    if (is_string($value)) {
                        $row[$key] = preg_replace('/\s+/', ' ', trim($value));
                    }
                }

                if (count($row) != 12) {
                    $errors[] = [
                        'row' => $index + 2,
                        'error' => 'Jumlah kolom harus 12'
                    ];
                    continue;
                }

                // Validasi wajib isi
                foreach ($requiredFields as $field) {

                    $value = $row[$field] ?? null;

                    if (is_null($value) || trim((string)$value) === '') {

                        $errors[] = [
                            'row' => $index + 2,
                            'column' => $field,
                            'error' => 'Kolom wajib diisi'
                        ];

                        continue 2;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Validasi Bidang Prestasi
                |--------------------------------------------------------------------------
                */

                $bidang = $this->normalizeKey($row['bidang_prestasi']);

                // apakah nama bidang pada excel valid?
                if (!isset($normalizedMapping[$bidang])) {

                    $errors[] = [
                        'row' => $index + 2,
                        'column' => 'bidang_prestasi',
                        'error' => 'Bidang prestasi tidak valid'
                    ];

                    continue;
                }

                $excelBidang = $normalizedMapping[$bidang];

                // apakah bidang sesuai halaman yang sedang dibuka?
                if ($excelBidang !== $bidang_prestasi) {

                    $errors[] = [
                        'row' => $index + 2,
                        'column' => 'bidang_prestasi',
                        'error' => "Halaman ini untuk import {$bidang_prestasi}, tetapi Excel berisi {$excelBidang}"
                    ];

                    continue;
                }

                // rapikan nilainya
                $row['bidang_prestasi'] = $excelBidang;

                /*
                |--------------------------------------------------------------------------
                | Validasi Tingkat
                |--------------------------------------------------------------------------
                */

                $tingkat = $this->normalizeKey($row['tingkat']);

                if (!isset($validTingkat[$tingkat])) {

                    $errors[] = [
                        'row' => $index + 2,
                        'column' => 'tingkat',
                        'error' => 'Tingkat harus Kabupaten/Kota, Provinsi, Nasional atau Internasional'
                    ];

                    continue;
                }

                $row['tingkat'] = $validTingkat[$tingkat];

                /*
                |--------------------------------------------------------------------------
                | Validasi Kategori Kegiatan
                |--------------------------------------------------------------------------
                */

                $kategori = $this->normalizeKey($row['kategori_kegiatan']);

                if (!isset($validKategoriKegiatan[$kategori])) {

                    $errors[] = [
                        'row' => $index + 2,
                        'column' => 'kategori_kegiatan',
                        'error' => 'Kategori kegiatan harus Individu atau Beregu'
                    ];

                    continue;
                }

                $row['kategori_kegiatan'] = $validKategoriKegiatan[$kategori];

                /*
                |--------------------------------------------------------------------------
                | Data hasil
                |--------------------------------------------------------------------------
                */

                $result[] = [
                    'madrasah_id' => $madrasah_id,
                    'bidang_prestasi' => $bidang_prestasi,
                    'submitter' => $submitter,
                    'nama_kegiatan' => $row['nama_kegiatan'],
                    'tingkat' => $row['tingkat'],
                    'kategori_kegiatan' => $row['kategori_kegiatan'],
                    'juara' => $row['juara'],
                    'lembaga_penyelenggara' => $row['lembaga_penyelenggara'],
                    'kategori_penyelenggara' => $row['kategori_penyelenggara'],
                    'waktu_kegiatan' => $row['waktu_kegiatan'],
                    'skor_luring' => $row['skor_luring'],
                    'skor_daring' => $row['skor_daring'],
                    'link_drive_bukti' => $row['link_drive_bukti'],
                    'keterangan' => $row['keterangan'] ?? null,
                    'periode' => $periode,
                ];
            }

            $groupedErrors = collect($errors)
                ->groupBy(function ($item) {
                    return ($item['column'] ?? 'general') . '|' . $item['error'];
                })
                ->map(function ($items) {
                    return [
                        'column' => $items->first()['column'] ?? null,
                        'message' => $items->first()['error'],
                        'rows' => $items->pluck('row')->toArray()
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => $result,
                'errors' => $groupedErrors,
                'redirect' => route('prestasi.preview', $jenis)
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function save_preview(Request $request,$jenis)
    {
        session([
            "preview_prestasi_$jenis" =>
                json_decode(
                    $request->data,
                    true
                )
        ]);

        return response()->json([
            'success'=>true
        ]);
    }

    public function preview($jenis)
    {
        $data = session(
            "preview_prestasi_$jenis",
            []
        );

        if(empty($data)){

            return redirect()
                ->route(
                    'prestasi.import',
                    $jenis
                );

        }

        return view(
            'prestasi.preview',
            compact(
                'jenis',
                'data'
            )
        );
    }

    public function store_import($jenis)
    {
        $data = session("preview_prestasi_$jenis", []);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data preview.'
            ], 422);
        }

        DB::beginTransaction();

        try {

            foreach ($data as $row) {
                PrestasiSiswa::create($row);
            }

            ActivityLogger::log(
                event: 'import',
                description: 'Import Data Prestasi',
                subject: new PrestasiSiswa(),
                properties: [
                    'jenis' => $jenis,
                    'jumlah_data' => count($data),
                    'madrasah_id' => auth()->user()->madrasah_id,
                    'nama_madrasah' => auth()->user()->madrasah->nama_madrasah,
                ]
            );

            DB::commit();

            session()->forget("preview_prestasi_$jenis");

            return response()->json([
                'success' => true
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Gagal import data prestasi', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'jenis' => $jenis,
                'jumlah_data' => count($data),
                'madrasah_id' => auth()->user()->madrasah_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            ], 500);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | FITUR CREATE, EDIT, DELETE
    |--------------------------------------------------------------------------
    */

    public function create($jenis)
    {
        $mapping = [
            'akademik' => 'Akademik',
            'non-akademik' => 'Non Akademik',
            'keagamaan' => 'Keagamaan',
            'gtk' => 'GTK',
            'lembaga' => 'Lembaga',
        ];

        $breadcrumb = breadcrumb([
            'Prestasi' => route('prestasi.index', $jenis),
            $mapping[$jenis] => route('prestasi.index', $jenis),
            'Tambah Prestasi '.$mapping[$jenis]
        ]);

        return view(
            'prestasi.form',
            [
                'jenis' => $jenis,
                'mode' => 'create',
                'prestasi' => null,
                'data' => null,
                'breadcrumb' => $breadcrumb
            ]
        );
    }

    public function store(Request $request, $jenis)
    {
        $validatedData = $request->validate([
            'bidang_prestasi' => 'required|in:Akademik,Non Akademik,Keagamaan,GTK,Lembaga',
            'nama_kegiatan' => 'required|string|max:255',
            'tingkat' => 'required|in:Kabupaten/Kota,Provinsi,Nasional,Internasional',
            'kategori_kegiatan' => 'required|in:Individu,Beregu',
            'juara' => 'required|string|max:255',
            'lembaga_penyelenggara' => 'nullable|string|max:255',
            'kategori_penyelenggara' => 'nullable|string|max:255',
            'waktu_kegiatan' => 'required|date',
            'skor_luring' => 'nullable|numeric',
            'skor_daring' => 'nullable|numeric',
            'link_drive_bukti' => 'nullable|url',
            'keterangan' => 'nullable|string',
        ]);


        try {
            DB::beginTransaction();

            $validatedData['madrasah_id'] = auth()->user()->madrasah_id;
            $validatedData['submitter'] = auth()->user()->nama;
            $validatedData['periode'] = now()->year;

            $prestasi = PrestasiSiswa::create($validatedData);

            ActivityLogger::log(
                event: 'create',
                description: 'Insert Data Prestasi ' . $prestasi->bidang_prestasi,
                subject: $prestasi,
                properties: $validatedData
            );

            DB::commit();

            return redirect()->route('prestasi.index', $jenis)->with('success', 'Data prestasi berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal menambahkan data prestasi', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $validatedData ?? null,
            ]);

            return redirect()->back()->withInput()->with('error', 'Data prestasi gagal ditambahkan.');
        }
    }

    public function edit($jenis, $id)
    {
        $mapping = [
            'akademik' => 'Akademik',
            'non-akademik' => 'Non Akademik',
            'keagamaan' => 'Keagamaan',
            'gtk' => 'GTK',
            'lembaga' => 'Lembaga',
        ];

        $prestasi = PrestasiSiswa::findOrFail($id);

        $breadcrumb = breadcrumb([
            'Prestasi' => route('prestasi.index', $jenis),
            $mapping[$jenis] => route('prestasi.index', $jenis),
            'Edit Prestasi ' . $mapping[$jenis]
        ]);

        return view('prestasi.form', compact('jenis', 'breadcrumb', 'prestasi'))
            ->with(['mode' => 'edit']);
    }

    public function update(Request $request, $jenis, $id)
    {
        $validatedData = $request->validate([
            'bidang_prestasi' => 'required|in:Akademik,Non Akademik,Keagamaan,GTK,Lembaga',
            'nama_kegiatan' => 'required|string|max:255',
            'tingkat' => 'required|in:Kabupaten/Kota,Provinsi,Nasional,Internasional',
            'kategori_kegiatan' => 'required|in:Individu,Beregu',
            'juara' => 'required|string|max:255',
            'lembaga_penyelenggara' => 'nullable|string|max:255',
            'kategori_penyelenggara' => 'nullable|string|max:255',
            'waktu_kegiatan' => 'required|date',
            'skor_luring' => 'nullable|numeric',
            'skor_daring' => 'nullable|numeric',
            'link_drive_bukti' => 'nullable|url',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $prestasi = PrestasiSiswa::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | Data lama sebelum update
            |--------------------------------------------------------------------------
            */
            $oldData = $prestasi->only([
                'bidang_prestasi',
                'nama_kegiatan',
                'tingkat',
                'kategori_kegiatan',
                'juara',
                'lembaga_penyelenggara',
                'kategori_penyelenggara',
                'waktu_kegiatan',
                'skor_luring',
                'skor_daring',
                'link_drive_bukti',
                'keterangan',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update data
            |--------------------------------------------------------------------------
            */
            $prestasi->update($validatedData);
            $prestasi->refresh();

            /*
            |--------------------------------------------------------------------------
            | Data baru setelah update
            |--------------------------------------------------------------------------
            */
            $newData = $prestasi->only([
                'bidang_prestasi',
                'nama_kegiatan',
                'tingkat',
                'kategori_kegiatan',
                'juara',
                'lembaga_penyelenggara',
                'kategori_penyelenggara',
                'waktu_kegiatan',
                'skor_luring',
                'skor_daring',
                'link_drive_bukti',
                'keterangan',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            ActivityLogger::log(
                event: 'update',
                description: 'Update Data Prestasi ' . $prestasi->bidang_prestasi,
                subject: $prestasi,
                properties: [
                    'old' => $oldData,
                    'new' => $newData,
                ]
            );

            DB::commit();

            return redirect()->route('prestasi.index', $jenis)
                ->with('success', 'Data prestasi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal update data prestasi', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'id' => $id,
                'data' => $validatedData ?? null,
            ]);

            return redirect()->back()->withInput()
                ->with('error', 'Data prestasi gagal diperbarui.');
        }
    }

    public function destroy($jenis, $id)
    {
        try {
            DB::beginTransaction();

            $prestasi = PrestasiSiswa::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | Backup data sebelum delete
            |--------------------------------------------------------------------------
            */

            $deletedData = $prestasi->only([
                'id',
                'bidang_prestasi',
                'nama_kegiatan',
                'tingkat',
                'kategori_kegiatan',
                'juara',
                'lembaga_penyelenggara',
                'kategori_penyelenggara',
                'waktu_kegiatan',
                'skor_luring',
                'skor_daring',
                'link_drive_bukti',
                'keterangan',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Delete data (Soft Delete)
            |--------------------------------------------------------------------------
            */

            $prestasi->delete();

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            ActivityLogger::log(
                event: 'delete',
                description: 'Menghapus data prestasi ' . $prestasi->bidang_prestasi,
                subject: $prestasi,
                properties: [
                    'deleted_data' => $deletedData,
                ]
            );

            DB::commit();

            return redirect()
                ->route('prestasi.index', $jenis)
                ->with('success', 'Data prestasi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal menghapus data prestasi', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'prestasi_id' => $id,
            ]);

            return redirect()
                ->route('prestasi.index', $jenis)
                ->with('error', 'Data prestasi gagal dihapus.');
        }
    }
}
