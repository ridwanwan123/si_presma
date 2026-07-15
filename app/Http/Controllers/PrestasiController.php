<?php

namespace App\Http\Controllers;

use App\Models\PrestasiSiswa;
use App\Models\PeriodeAktif;
use App\Exports\PrestasiMadrasahExport;
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

    /*
    |--------------------------------------------------------------------------
    | CEK AKSES SIKLUS PRESTASI
    |--------------------------------------------------------------------------
    */
    private function cekAksesSiklus()
    {
        $siklus = auth()->user()->madrasah->prestasiSiklusAktif();

        if (!$siklus->canInput()) {

            $message = 'Data prestasi tidak dapat diubah karena proses penilaian telah dimulai.';

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        return null;
    }

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
            ->where('periode', PeriodeAktif::aktif())
            ->where(
                'bidang_prestasi',
                $mapping[$jenis]
            );

        $summary = (clone $query)
            ->selectRaw("
                COUNT(*) as total_prestasi,

                SUM(CASE WHEN tingkat = 'Kabupaten/Kota' THEN 1 ELSE 0 END) as kabupaten,
                SUM(CASE WHEN tingkat = 'Provinsi' THEN 1 ELSE 0 END) as provinsi,
                SUM(CASE WHEN tingkat = 'Nasional' THEN 1 ELSE 0 END) as nasional,
                SUM(CASE WHEN tingkat = 'Internasional' THEN 1 ELSE 0 END) as internasional,

                COALESCE(SUM(CASE WHEN metode_pelaksanaan = 'Luring' THEN skor ELSE 0 END), 0) as total_skor_luring,
                COALESCE(SUM(CASE WHEN metode_pelaksanaan = 'Daring' THEN skor ELSE 0 END), 0) as total_skor_daring
            ")
            ->first();

        $breadcrumb = breadcrumb([
            'Prestasi' => route('prestasi.index', $jenis),
            $mapping[$jenis]
        ]);

        $siklus = auth()->user()->madrasah->prestasiSiklusAktif();

        return view(
            'prestasi.index',
            compact(
                'jenis',
                'summary',
                'breadcrumb',
                'siklus'
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
            ->where('periode', PeriodeAktif::aktif())
            ->where('bidang_prestasi', $mapping[$jenis])
            ->latest();

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('waktu_kegiatan', function ($item) {
                return optional($item->waktu_kegiatan)
                    ->format('d M Y');
            })

            ->editColumn('skor', function ($item) {
                return $item->skor !== null
                    ? number_format($item->skor, 0, ',', '.')
                    : null;
            })

            ->filter(function ($query) {

                if (request()->has('search')) {

                    $keyword = request('search')['value'];

                    if (!empty($keyword)) {

                        $query->where(function ($q) use ($keyword) {

                            $q->where('nama_kegiatan', 'like', "%{$keyword}%")
                            ->orWhere('kategori_kegiatan', 'like', "%{$keyword}%")
                            ->orWhere('lembaga_penyelenggara', 'like', "%{$keyword}%")
                            ->orWhere('kategori_penyelenggara', 'like', "%{$keyword}%")
                            ->orWhere('keterangan', 'like', "%{$keyword}%")
                            ->orWhere('bidang_prestasi', 'like', "%{$keyword}%")
                            ->orWhere('tingkat', 'like', "%{$keyword}%");
                        });
                    }
                }
            })

            ->make(true);
    }

    /*
    |--------------------------------------------------------------------------
    | PILIH METODE (ENTRY POINT "TAMBAH PRESTASI")
    |--------------------------------------------------------------------------
    */
    public function pilihMetode()
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        $breadcrumb = breadcrumb([
            'Tambah Prestasi'
        ]);

        return view('prestasi.create', compact('breadcrumb'));
    }

    /*
    |--------------------------------------------------------------------------
    | FITUR IMPORT
    |--------------------------------------------------------------------------
    */
    public function template()
    {
        return Excel::download(
            new PrestasiTemplateExport,
            'template-prestasi.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT DATA PRESTASI (SISI MADRASAH, DATA MENTAH — BUKAN HASIL PENILAIAN)
    |--------------------------------------------------------------------------
    | Format mengikuti template resmi Kanwil Kemenag DKI Jakarta, supaya bisa
    | langsung dicetak. ?periode= opsional, default ke periode aktif.
    */
    public function export(Request $request)
    {
        $madrasah = auth()->user()->madrasah;

        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();

        $namaFile = 'Data-Prestasi-'
            . str_replace(' ', '-', $madrasah->nama_madrasah)
            . '-' . $periode . '.xlsx';

        return Excel::download(
            new PrestasiMadrasahExport($madrasah, $periode),
            $namaFile
        );
    }

    public function import()
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        $breadcrumb = breadcrumb([
            'Tambah Prestasi' => route('prestasi.tambah'),
            'Import Excel'
        ]);

        return view('prestasi.import', compact('breadcrumb'));
    }

    public function upload(Request $request)
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

    public function checking_import_prestasi(Request $request)
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

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
        $periode = PeriodeAktif::aktif();

        $requiredFields = [
            'bidang_prestasi',
            'nama_kegiatan',
            'tingkat',
            'kategori_kegiatan',
            'juara',
            'lembaga_penyelenggara',
            'kategori_penyelenggara',
            'waktu_kegiatan',
            'metode_pelaksanaan',
            'skor',
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

        $validMetodePelaksanaan = [
            'luring' => 'Luring',
            'daring' => 'Daring',
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
                | Validasi Bidang Prestasi (dibaca dari isi Excel, bukan dari halaman)
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

                // rapikan nilainya (tidak lagi dipaksa sesuai halaman/route)
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
                | Validasi Metode Pelaksanaan
                |--------------------------------------------------------------------------
                */

                $metode = $this->normalizeKey($row['metode_pelaksanaan']);

                if (!isset($validMetodePelaksanaan[$metode])) {

                    $errors[] = [
                        'row' => $index + 2,
                        'column' => 'metode_pelaksanaan',
                        'error' => 'Metode pelaksanaan harus Luring atau Daring'
                    ];

                    continue;
                }

                $row['metode_pelaksanaan'] = $validMetodePelaksanaan[$metode];

                /*
                |--------------------------------------------------------------------------
                | Validasi Skor
                |--------------------------------------------------------------------------
                */

                if (!is_numeric($row['skor'])) {

                    $errors[] = [
                        'row' => $index + 2,
                        'column' => 'skor',
                        'error' => 'Skor harus berupa angka'
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Data hasil
                |--------------------------------------------------------------------------
                */

                $result[] = [
                    'madrasah_id' => $madrasah_id,
                    'bidang_prestasi' => $excelBidang,
                    'submitter' => $submitter,
                    'nama_kegiatan' => $row['nama_kegiatan'],
                    'tingkat' => $row['tingkat'],
                    'kategori_kegiatan' => $row['kategori_kegiatan'],
                    'juara' => $row['juara'],
                    'lembaga_penyelenggara' => $row['lembaga_penyelenggara'],
                    'kategori_penyelenggara' => $row['kategori_penyelenggara'],
                    'waktu_kegiatan' => $row['waktu_kegiatan'],
                    'metode_pelaksanaan' => $row['metode_pelaksanaan'],
                    'skor' => $row['skor'],
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
                'redirect' => route('prestasi.preview')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function save_preview(Request $request)
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        session([
            'preview_prestasi' =>
                json_decode(
                    $request->data,
                    true
                )
        ]);

        return response()->json([
            'success'=>true
        ]);
    }

    public function preview()
    {
        $data = session('preview_prestasi', []);

        if(empty($data)){

            return redirect()->route('prestasi.import');

        }

        return view(
            'prestasi.preview',
            compact('data')
        );
    }

    public function store_import()
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        $data = session('preview_prestasi', []);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data preview.'
            ], 422);
        }

        $bidangSlugMap = [
            'Akademik' => 'akademik',
            'Non Akademik' => 'non-akademik',
            'Keagamaan' => 'keagamaan',
            'GTK' => 'gtk',
            'Lembaga' => 'lembaga',
        ];

        $bidangTersimpan = collect($data)->pluck('bidang_prestasi')->filter()->unique()->values();

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
                    'bidang' => $bidangTersimpan->toArray(),
                    'jumlah_data' => count($data),
                    'madrasah_id' => auth()->user()->madrasah_id,
                    'nama_madrasah' => auth()->user()->madrasah->nama_madrasah,
                ]
            );

            DB::commit();

            session()->forget('preview_prestasi');

            // Arahkan ke daftar bidang dari baris pertama yang berhasil diimport
            $jenisTujuan = $bidangSlugMap[$bidangTersimpan->first()] ?? null;

            return response()->json([
                'success' => true,
                'redirect' => $jenisTujuan
                    ? route('prestasi.index', $jenisTujuan)
                    : route('prestasi.tambah')
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Gagal import data prestasi', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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

    public function create()
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        $breadcrumb = breadcrumb([
            'Tambah Prestasi' => route('prestasi.tambah'),
            'Input Manual'
        ]);

        return view(
            'prestasi.form',
            [
                'jenis' => null,
                'mode' => 'create',
                'prestasi' => null,
                'data' => null,
                'breadcrumb' => $breadcrumb
            ]
        );
    }

    public function store(Request $request)
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        $validatedData = $request->validate([
            'bidang_prestasi' => 'required|in:Akademik,Non Akademik,Keagamaan,GTK,Lembaga',
            'nama_kegiatan' => 'required|string|max:255',
            'tingkat' => 'required|in:Kabupaten/Kota,Provinsi,Nasional,Internasional',
            'kategori_kegiatan' => 'required|in:Individu,Beregu',
            'juara' => 'required|string|max:255',
            'lembaga_penyelenggara' => 'nullable|string|max:255',
            'kategori_penyelenggara' => 'nullable|string|max:255',
            'waktu_kegiatan' => 'required|date',
            'metode_pelaksanaan' => 'required|in:Luring,Daring',
            'skor' => 'nullable|numeric',
            'link_drive_bukti' => 'nullable|url',
            'keterangan' => 'nullable|string',
        ]);

        $bidangSlugMap = [
            'Akademik' => 'akademik',
            'Non Akademik' => 'non-akademik',
            'Keagamaan' => 'keagamaan',
            'GTK' => 'gtk',
            'Lembaga' => 'lembaga',
        ];

        try {
            DB::beginTransaction();

            $validatedData['madrasah_id'] = auth()->user()->madrasah_id;
            $validatedData['submitter'] = auth()->user()->nama;
            $validatedData['periode'] = PeriodeAktif::aktif();

            $prestasi = PrestasiSiswa::create($validatedData);

            ActivityLogger::log(
                event: 'create',
                description: 'Insert Data Prestasi ' . $prestasi->bidang_prestasi,
                subject: $prestasi,
                properties: $validatedData
            );

            DB::commit();

            $jenisTujuan = $bidangSlugMap[$prestasi->bidang_prestasi] ?? null;

            return redirect()
                ->route('prestasi.index', $jenisTujuan ?? 'akademik')
                ->with('success', 'Data prestasi berhasil ditambahkan.');
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
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

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
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        $validatedData = $request->validate([
            'bidang_prestasi' => 'required|in:Akademik,Non Akademik,Keagamaan,GTK,Lembaga',
            'nama_kegiatan' => 'required|string|max:255',
            'tingkat' => 'required|in:Kabupaten/Kota,Provinsi,Nasional,Internasional',
            'kategori_kegiatan' => 'required|in:Individu,Beregu',
            'juara' => 'required|string|max:255',
            'lembaga_penyelenggara' => 'nullable|string|max:255',
            'kategori_penyelenggara' => 'nullable|string|max:255',
            'waktu_kegiatan' => 'required|date',
            'metode_pelaksanaan' => 'required|in:Luring,Daring',
            'skor' => 'nullable|numeric',
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
                'metode_pelaksanaan',
                'skor',
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
                'metode_pelaksanaan',
                'skor',
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
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

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
                'metode_pelaksanaan',
                'skor',
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