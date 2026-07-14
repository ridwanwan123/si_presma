<?php

namespace App\Http\Controllers;

use App\Models\AssignAsesor;
use App\Models\Madrasah;
use App\Models\PrestasiSiklus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AssignAsesorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HELPER: PERIODE & PIPELINE MADRASAH
    |--------------------------------------------------------------------------
    | "Pipeline" = madrasah yang siklus prestasinya untuk periode berjalan
    | sudah SUBMITTED (menunggu ditugaskan) atau ASSESSMENT (sudah
    | ditugaskan, sedang dinilai). Madrasah yang masih OPEN (belum submit)
    | atau sudah FINISHED tidak relevan untuk halaman Assign Asesor.
    */

    private function periodeAktif(): int
    {
        return now()->year;
    }

    private function madrasahIdsPipeline()
    {
        return PrestasiSiklus::where('periode', $this->periodeAktif())
            ->whereIn('status', [
                PrestasiSiklus::SUBMITTED,
                PrestasiSiklus::ASSESSMENT,
            ])
            ->pluck('madrasah_id');
    }

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalAsesor = User::whereHas('role', function ($q) {
            $q->where('nama', 'Pengawas');
        })->count();

        $madrasahIdsPipeline = $this->madrasahIdsPipeline();

        $totalMadrasah = $madrasahIdsPipeline->count();

        $sudahAssigned = AssignAsesor::whereIn('madrasah_id', $madrasahIdsPipeline)->count();

        $belumAssigned = max(0, $totalMadrasah - $sudahAssigned);

        $persenAssigned = $totalMadrasah > 0
            ? round(($sudahAssigned / $totalMadrasah) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | FILTER OPTION
        |--------------------------------------------------------------------------
        */

        $daftarAsesor = User::whereHas('role', function ($q) {
                $q->where('nama', 'Pengawas');
            })
            ->orderBy('nama')
            ->get();

        $jenjang = Madrasah::whereIn('id', $madrasahIdsPipeline)
            ->select('jenjang_madrasah')
            ->distinct()
            ->orderBy('jenjang_madrasah')
            ->pluck('jenjang_madrasah');

        $wilayah = Madrasah::whereIn('id', $madrasahIdsPipeline)
            ->select('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');

        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        $madrasahs = Madrasah::query()
            ->whereIn('id', $madrasahIdsPipeline)
            ->withCount('prestasis')
            ->with([
                'assignAsesor.asesor',
            ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER : SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $madrasahs->where(function ($q) use ($request) {

                $q->where(
                    'nama_madrasah',
                    'like',
                    '%' . $request->search . '%'
                )->orWhere(
                    'npsn',
                    'like',
                    '%' . $request->search . '%'
                );

            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER : JENJANG
        |--------------------------------------------------------------------------
        */

        if ($request->filled('jenjang')) {

            $madrasahs->where(
                'jenjang_madrasah',
                $request->jenjang
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER : WILAYAH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('wilayah')) {

            $madrasahs->where(
                'kota',
                $request->wilayah
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER : STATUS ASSIGN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            if ($request->status === 'assigned') {

                $madrasahs->has('assignAsesor');

            } elseif ($request->status === 'unassigned') {

                $madrasahs->doesntHave('assignAsesor');

            }

        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $madrasahs = $madrasahs
            ->orderBy('nama_madrasah')
            ->paginate(10)
            ->withQueryString();
        

        /*
        |--------------------------------------------------------------------------
        | BREADCRUMB
        |--------------------------------------------------------------------------
        */

        $breadcrumb = breadcrumb([
            'Assign Asesor'
        ]);

        return view('assignAsesor.index', compact(
            'breadcrumb',

            'totalAsesor',
            'totalMadrasah',
            'sudahAssigned',
            'belumAssigned',
            'persenAssigned',

            'daftarAsesor',
            'jenjang',
            'wilayah',

            'madrasahs'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    | Dipakai untuk assign single maupun assign massal (dipanggil berulang
    | dari JS untuk tiap madrasah terpilih). Karena AssignAsesor::hasOne per
    | Madrasah, updateOrCreate berbasis madrasah_id otomatis menangani rule:
    | belum ada assignment -> INSERT, sudah ada -> UPDATE record yang sama.
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'madrasah_id' => 'required|exists:madrasahs,id',
            'asesor_id'   => 'required|exists:users,id',
        ]);

        $siklus = PrestasiSiklus::where('madrasah_id', $validated['madrasah_id'])
            ->where('periode', $this->periodeAktif())
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Boleh assign kalau:
        | - canAssignAsesor() -> status SUBMITTED (assign pertama kali), atau
        | - isAssessment()    -> status ASSESSMENT (ubah/reassign asesor
        |                        yang sudah pernah ditugaskan sebelumnya)
        | Keduanya helper yang sudah ada di model PrestasiSiklus.
        |--------------------------------------------------------------------------
        */
        if (!$siklus || (!$siklus->canAssignAsesor() && !$siklus->isAssessment())) {

            $message = 'Madrasah ini belum mengirim pengajuan prestasi (status siklus bukan SUBMITTED/ASSESSMENT), sehingga tidak dapat ditugaskan asesor.';

            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()
                ->route('assign-asesor.index')
                ->with('error', $message);
        }

        $assignAsesor = DB::transaction(function () use ($validated, $siklus) {

            $assignAsesor = AssignAsesor::updateOrCreate(
                [
                    'madrasah_id' => $validated['madrasah_id'],
                ],
                [
                    'asesor_id'   => $validated['asesor_id'],
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Milestone berubah hanya saat assign PERTAMA kali (dari SUBMITTED).
            | Kalau ini reassign (siklus sudah ASSESSMENT), status tidak diubah lagi.
            |--------------------------------------------------------------------------
            */
            if ($siklus->canAssignAsesor()) {
                $siklus->update([
                    'status' => PrestasiSiklus::ASSESSMENT,
                    'assessment_started_at' => now(),
                ]);
            }

            return $assignAsesor;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Asesor berhasil ditugaskan.',
                'data'    => $assignAsesor,
            ]);
        }

        return redirect()
            ->route('assign-asesor.index')
            ->with('success', 'Asesor berhasil ditugaskan.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    | Untuk edit langsung record assign_asesors lewat id-nya sendiri
    | (belum dipakai oleh UI saat ini, disediakan karena route resource
    | mengaktifkan method ini).
    */

    public function update(Request $request, AssignAsesor $assign_asesor)
    {
        $validated = $request->validate([
            'asesor_id' => 'required|exists:users,id',
        ]);

        $assign_asesor->update([
            'asesor_id'   => $validated['asesor_id'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Assignment berhasil diperbarui.',
                'data'    => $assign_asesor,
            ]);
        }

        return redirect()
            ->route('assign-asesor.index')
            ->with('success', 'Assignment berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | FILTERED QUERY (dipakai khusus untuk export PDF)
    |--------------------------------------------------------------------------
    | Logic filter sengaja dipisah ke sini (bukan mengubah index()) supaya
    | fitur Assign Asesor yang sudah ada tetap utuh, sekaligus hasil PDF
    | tetap mengikuti filter yang sedang aktif di halaman (search, jenjang,
    | wilayah, status).
    */

    private function filteredMadrasahQuery(Request $request)
    {
        $query = Madrasah::query()
            ->whereIn('id', $this->madrasahIdsPipeline())
            ->withCount('prestasis')
            ->with(['assignAsesor.asesor']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where(
                    'nama_madrasah',
                    'like',
                    '%' . $request->search . '%'
                )->orWhere(
                    'npsn',
                    'like',
                    '%' . $request->search . '%'
                );
            });
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang_madrasah', $request->jenjang);
        }

        if ($request->filled('wilayah')) {
            $query->where('kota', $request->wilayah);
        }

        if ($request->filled('status')) {
            if ($request->status === 'assigned') {
                $query->has('assignAsesor');
            } elseif ($request->status === 'unassigned') {
                $query->doesntHave('assignAsesor');
            }
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    | Laporan penugasan asesor, mengikuti filter yang sedang aktif di
    | halaman (kalau tidak ada filter, berarti seluruh madrasah peserta).
    */

    public function exportPdf(Request $request)
    {
        $madrasahs = $this->filteredMadrasahQuery($request)
            ->orderBy('nama_madrasah')
            ->get();

        $totalAsesor = User::whereHas('role', function ($q) {
            $q->where('nama', 'Pengawas');
        })->count();

        $totalMadrasah = $madrasahs->count();
        $sudahAssigned = $madrasahs->filter(fn ($m) => $m->assignAsesor !== null)->count();
        $belumAssigned = $totalMadrasah - $sudahAssigned;

        // Kelompokkan madrasah per asesor untuk kebutuhan rowspan di PDF
        $grouped = $madrasahs->groupBy(function ($madrasah) {
            return $madrasah->assignAsesor?->asesor?->nama ?? '__BELUM_DITUGASKAN__';
        })->sortKeys();

        $belumDitugaskanGroup = $grouped->pull('__BELUM_DITUGASKAN__');
        if ($belumDitugaskanGroup) {
            $grouped->put('Belum Ditugaskan', $belumDitugaskanGroup);
        }

        // Ringkasan beban kerja, diurutkan dari yang paling banyak pegang madrasah
        $bebanAsesor = $grouped
            ->reject(fn ($items, $nama) => $nama === 'Belum Ditugaskan')
            ->map(fn ($items, $nama) => ['nama' => $nama, 'jumlah' => $items->count()])
            ->values()
            ->sortByDesc('jumlah')
            ->values();

        $dicetakOleh = auth()->user()->nama ?? '-';
        $tanggalCetak = now()->format('d/m/Y H:i');

        $pdf = Pdf::loadView('assignAsesor.pdf', compact(
            'grouped', 'bebanAsesor', 'totalAsesor', 'totalMadrasah',
            'sudahAssigned', 'belumAssigned', 'dicetakOleh', 'tanggalCetak'
        ))->setPaper('F4', 'portrait');

        return $pdf->stream('laporan-penugasan-asesor-' . now()->format('Ymd_His') . '.pdf');
    }
}