<?php

namespace App\Http\Controllers;

use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PrestasiSiswaImport;
use App\Exports\PrestasiTemplateExport;

class PrestasiController extends Controller
{
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

        $user = auth()->user();

        if ($user->isOperator()) {

            $query->where(
                'madrasah_id',
                $user->madrasah_id
            );

        }

        $prestasi = (clone $query)
            ->latest()
            ->paginate(10);

        $summary = (clone $query)
            ->selectRaw("
                COUNT(*) as total,
                SUM(status_verifikasi = 'verified') as verified,
                SUM(status_verifikasi = 'pending') as pending,
                SUM(status_verifikasi = 'rejected') as rejected
            ")
            ->first();

        $breadcrumb = breadcrumb([
            'Prestasi ' => route('prestasi.index', $jenis),
            $mapping[$jenis]
        ]);

        return view(
            'prestasi.index',
            compact(
                'jenis',
                'prestasi',
                'summary',
                'breadcrumb'
            )
        );
    }
    
    public function data($jenis)
    {
        return response()->json([
            'data' => PrestasiSiswa::where(
                'bidang_prestasi',
                ucfirst($jenis)
            )->get()
        ]);
    }

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

    public function checking_import_prestasi(Request $request,$jenis)
    {
        $request->validate([
            'file_import'=>'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $mapping = [
            'akademik' => 'Akademik',
            'non-akademik' => 'Non Akademik',
            'keagamaan' => 'Keagamaan',
            'gtk' => 'GTK',
            'lembaga' => 'Lembaga',
        ];

        $madrasah_id = auth()->user()->madrasah_id;
        $submitter = auth()->user()->nama;
        $bidang_prestasi = $mapping[$jenis];
        $periode = date('Y-m-d');

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

        $nullableFields = [
            'keterangan',
            'periode',
        ];

        $errors = [];
        $result = [];

        try {
            $import = new PrestasiSiswaImport();
            Excel::import(
                $import,
                $request->file('file_import')
            );

            $data = $import->rows;

            
            foreach($data as $index=>$row){
                if(count($row) != 13){
                    $errors[]=[
                        'row'=>$index+2,
                        'error'=>'Jumlah kolom harus 13'
                    ];
                    continue;
                }

                foreach ($requiredFields as $field) {
                    $value = $row[$field] ?? null;

                    if (is_null($value)|| trim((string) $value) === '') {
                        $errors[] = [
                            'row'    => $index + 2,
                            'column' => $field,
                            'error'  => 'Kolom wajib diisi'
                        ];

                        continue 2;
                    }
                }

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
                    'keterangan' => $row['keterangan'],
                    'periode' => $periode
                ];
            }

            return response()->json([
                'success'=>true,
                'data'=>$result,
                'errors'=>$errors,
                'redirect'=>route('prestasi.preview',$jenis)
            ]);
        } catch(\Throwable $e){
            return response()->json([
                'message'=>$e->getMessage()
            ],500);
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

        if(empty($data)){
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data preview.'
            ],422);
        }

        foreach($data as $row){
            PrestasiSiswa::create($row);
        }

        session()->forget("preview_prestasi_$jenis");

        return response()->json([
            'success' => true
        ]);
    }
}
