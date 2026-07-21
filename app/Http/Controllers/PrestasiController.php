<?php

namespace App\Http\Controllers;

use App\Models\PrestasiSiswa;
use App\Models\PeriodeAktif;
use App\Models\Madrasah;
use App\Exports\PrestasiMadrasahExport;
use App\Services\PrestasiImportService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
    | SERVICE IMPORT (dependency injection lewat constructor)
    |--------------------------------------------------------------------------
    */
    public function __construct(
        private PrestasiImportService $importService
    ) {
    }

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
    public function index(Request $request, $jenis)
    {
        $mapping = [
            'akademik' => 'Akademik',
            'non-akademik' => 'Non Akademik',
            'keagamaan' => 'Keagamaan',
            'gtk' => 'GTK',
            'lembaga' => 'Lembaga',
        ];

        $user = auth()->user();
        $isAdmin = $user->hasRole('Administrator');

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

        /*
        |--------------------------------------------------------------------------
        | SIKLUS PRESTASI — HANYA RELEVAN UNTUK MADRASAH
        |--------------------------------------------------------------------------
        | Administrator tidak punya relasi madrasah() sama sekali, dan yang
        | lebih penting: Admin melihat SEMUA madrasah sekaligus di halaman
        | ini, masing-masing bisa punya status siklus yang BEDA-BEDA (ada
        | yang masih OPEN, ada yang sudah FINISHED) -- jadi "satu banner
        | siklus" untuk semuanya tidak masuk akal buat ditampilkan.
        |
        | $canInput SENGAJA dihitung di sini (bukan di blade) supaya sudah
        | jadi boolean polos, aman dipakai langsung tanpa null-check di view.
        |--------------------------------------------------------------------------
        */
        $siklus = $isAdmin ? null : $user->madrasah->prestasiSiklusAktif();
        $canInput = $isAdmin ? false : $siklus->canInput();

        /*
        |--------------------------------------------------------------------------
        | FILTER MADRASAH — HANYA UNTUK ADMINISTRATOR
        |--------------------------------------------------------------------------
        */
        $daftarMadrasah = $isAdmin
            ? Madrasah::orderBy('nama_madrasah')->get(['id', 'nama_madrasah'])
            : collect();

        $madrasahFilter = $isAdmin ? ($request->integer('madrasah_id') ?: null) : null;

        return view(
            'prestasi.index',
            compact(
                'jenis',
                'summary',
                'breadcrumb',
                'siklus',
                'isAdmin',
                'canInput',
                'daftarMadrasah',
                'madrasahFilter'
            )
        );
    }

    public function data(Request $request, $jenis)
    {
        $mapping = [
            'akademik'     => 'Akademik',
            'non-akademik' => 'Non Akademik',
            'keagamaan'    => 'Keagamaan',
            'gtk'          => 'GTK',
            'lembaga'      => 'Lembaga',
        ];

        $user = auth()->user();
        $isAdmin = $user->hasRole('Administrator');

        $query = PrestasiSiswa::visible()
            ->with('madrasah:id,nama_madrasah')
            ->where('periode', PeriodeAktif::aktif())
            ->where('bidang_prestasi', $mapping[$jenis])
            ->when(
                $isAdmin && $request->filled('madrasah_id'),
                fn ($q) => $q->where('madrasah_id', $request->integer('madrasah_id'))
            )
            ->latest();

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('nama_madrasah', function ($item) {
                return optional($item->madrasah)->nama_madrasah ?? '-';
            })

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
                            ->orWhere('tingkat', 'like', "%{$keyword}%")
                            ->orWhereHas('madrasah', function ($mq) use ($keyword) {
                                $mq->where('nama_madrasah', 'like', "%{$keyword}%");
                            });
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

    public function checking_import_prestasi(Request $request)
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        // Batas ukuran file dinaikkan dari 2MB -> 20MB, supaya file Excel
        // 5.000-20.000 baris tidak langsung ditolak sebelum sempat diproses.
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        // Jaring pengaman di level kode untuk import besar. Idealnya
        // memory_limit, max_execution_time, upload_max_filesize, dan
        // post_max_size di php.ini/vhost juga ikut disesuaikan untuk
        // endpoint ini (upload_max_filesize & post_max_size TIDAK bisa
        // diubah lewat ini_set(), harus di php.ini/server).
        @ini_set('memory_limit', '512M');
        set_time_limit(300);

        $madrasahId = auth()->user()->madrasah_id;
        $submitter = auth()->user()->nama;
        $periode = PeriodeAktif::aktif();

        try {

            $validated = $this->importService->validateFile(
                $request->file('file_import'),
                $madrasahId,
                $submitter,
                $periode
            );

            $response = [
                'success' => true,
                'errors' => $validated['errors'],
                'redirect' => route('prestasi.preview'),
            ];

            if (empty($validated['errors'])) {

                /*
                |--------------------------------------------------------------------------
                | Data tervalidasi langsung ditulis ke temp storage + token
                | disimpan di session DI SINI JUGA -- supaya client TIDAK
                | perlu lagi kirim balik seluruh data ke save_preview().
                | Sebelumnya alurnya: server kirim seluruh data ke client,
                | client kirim balik data yang PERSIS SAMA ke save_preview,
                | padahal tidak pernah dipakai/ditampilkan di browser sama
                | sekali -- dobel transfer payload besar yang sia-sia.
                |--------------------------------------------------------------------------
                */
                $token = $this->importService->storeTemp($validated['result']);

                session(['preview_prestasi_token' => $token]);
            }

            return response()->json($response);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE PREVIEW
    |--------------------------------------------------------------------------
    | Sejak checking_import_prestasi() langsung menyimpan token ke session,
    | endpoint ini SUDAH TIDAK DIPANGGIL LAGI oleh alur utama (lihat JS di
    | prestasi.import) -- TETAP DIPERTAHANKAN (bukan dihapus/route tidak
    | diubah) untuk kompatibilitas ke belakang kalau ada pemanggil lain.
    | Kalau dipanggil, tetap aman: data yang dikirim disimpan lewat jalur
    | temp storage yang sama (bukan langsung ke session mentah).
    |--------------------------------------------------------------------------
    */
    public function save_preview(Request $request)
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        $data = json_decode($request->data, true) ?? [];

        $token = $this->importService->storeTemp($data);

        session(['preview_prestasi_token' => $token]);

        return response()->json([
            'success' => true
        ]);
    }

    public function preview()
    {
        $token = session('preview_prestasi_token');

        $data = $this->importService->readTemp($token);

        if (empty($data)) {

            return redirect()->route('prestasi.import');

        }

        /*
        |--------------------------------------------------------------------------
        | PAGINASI MANUAL UNTUK PREVIEW
        |--------------------------------------------------------------------------
        | $data sumbernya array PHP dari temp file (bukan query Eloquent),
        | jadi tidak bisa langsung pakai ->paginate() bawaan query builder.
        | LengthAwarePaginator "membungkus" potongan array (array_slice)
        | supaya tetap dapat link pagination Bootstrap yang sama seperti
        | hasil ->paginate() biasa. Ini menghindari render ribuan <tr>
        | sekaligus di satu halaman kalau import sampai 5.000-20.000 baris.
        |
        | store_import() TIDAK terpengaruh sama sekali oleh ini -- dia
        | tetap membaca SELURUH $data dari temp file langsung, bukan dari
        | yang sedang ditampilkan di halaman preview.
        */
        $perPage = 50;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $items = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

        $paginatedData = new LengthAwarePaginator(
            $items,
            count($data),
            $perPage,
            $currentPage,
            ['path' => route('prestasi.preview')]
        );

        // Ringkasan (total, daftar bidang, submitter) tetap dihitung dari
        // SELURUH data, bukan cuma yang tampil di halaman aktif.
        $totalData = count($data);
        $bidangList = collect($data)->pluck('bidang_prestasi')->filter()->unique()->values();
        $submitter = $data[0]['submitter'] ?? '-';

        return view(
            'prestasi.preview',
            compact('paginatedData', 'totalData', 'bidangList', 'submitter')
        );
    }

    public function store_import()
    {
        if ($response = $this->cekAksesSiklus()) {
            return $response;
        }

        $token = session('preview_prestasi_token');

        $data = $this->importService->readTemp($token);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data preview.'
            ], 422);
        }

        @ini_set('memory_limit', '512M');
        set_time_limit(300);

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

            /*
            |--------------------------------------------------------------------------
            | Dari N query INSERT terpisah (satu per baris, lewat Eloquent
            | create()) menjadi ceil(N / 500) query saja lewat bulk insert().
            | Ini perbaikan performa paling besar di seluruh alur import --
            | 20.000 baris = 20.000 query dulu, sekarang cuma ~40 query.
            |--------------------------------------------------------------------------
            */
            $totalTersimpan = $this->importService->bulkInsert($data);

            ActivityLogger::log(
                event: 'import',
                description: 'Import Data Prestasi',
                subject: new PrestasiSiswa(),
                properties: [
                    'bidang' => $bidangTersimpan->toArray(),
                    'jumlah_data' => $totalTersimpan,
                    'madrasah_id' => auth()->user()->madrasah_id,
                    'nama_madrasah' => auth()->user()->madrasah->nama_madrasah,
                ]
            );

            DB::commit();

            $this->importService->deleteTemp($token);
            session()->forget('preview_prestasi_token');

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
            'juara' => 'required|in:Juara 1,Juara 2,Juara 3,Harapan 1,Harapan 2,Harapan 3',
            'lembaga_penyelenggara' => 'nullable|string|max:255',
            'kategori_penyelenggara' => 'required|in:Pemerintah,Non Pemerintah',
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
            'juara' => 'required|in:Juara 1,Juara 2,Juara 3,Harapan 1,Harapan 2,Harapan 3',
            'lembaga_penyelenggara' => 'nullable|string|max:255',
            'kategori_penyelenggara' => 'required|in:Pemerintah,Non Pemerintah',
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