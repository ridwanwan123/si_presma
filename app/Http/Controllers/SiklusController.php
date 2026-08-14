<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\AssignAsesor;
use App\Models\Madrasah;
use App\Models\PeriodeAktif;
use App\Models\PrestasiSiklus;
use App\Models\PrestasiSiswa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class SiklusController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LABEL & WARNA STATUS -- SATU-SATUNYA SUMBER KEBENARAN
    |--------------------------------------------------------------------------
    | Sama persis dengan label yang dipakai di halaman Pengajuan Prestasi
    | (pengajuan.blade.php), supaya istilah yang dilihat madrasah dan yang
    | dilihat admin di sini selalu konsisten.
    |--------------------------------------------------------------------------
    */
    private const STATUS_INFO = [
        PrestasiSiklus::OPEN => [
            'label' => 'Terbuka untuk Pengisian',
            'icon'  => 'bi-unlock-fill',
            'badge' => 'badge-open',
        ],
        PrestasiSiklus::SUBMITTED => [
            'label' => 'Menunggu Penugasan Asesor',
            'icon'  => 'bi-send-check-fill',
            'badge' => 'badge-submitted',
        ],
        PrestasiSiklus::ASSESSMENT => [
            'label' => 'Sedang Dinilai Asesor',
            'icon'  => 'bi-clipboard-data-fill',
            'badge' => 'badge-assessment',
        ],
        PrestasiSiklus::FINISHED => [
            'label' => 'Penilaian Selesai',
            'icon'  => 'bi-check-circle-fill',
            'badge' => 'badge-finished',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | MONITORING STATUS SIKLUS SELURUH MADRASAH (PER PERIODE)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $periode = $request->integer('periode') ?: PeriodeAktif::aktif();

        $jenjangFilter = $request->query('jenjang') ?: null;
        $statusMadrasahFilter = $request->query('status_madrasah') ?: null;
        $kotaFilter = $request->query('kota') ?: null;
        $statusSiklusFilter = $request->query('status_siklus') ?: null;
        $search = $request->query('search') ?: null;

        $madrasahIdsStatus = $statusMadrasahFilter
            ? Madrasah::where('status_madrasah', $statusMadrasahFilter)->pluck('id')
            : null;

        $daftarSiklus = PrestasiSiklus::with('madrasah')
            ->where('periode', $periode)
            ->when($statusSiklusFilter, fn ($q) => $q->where('status', $statusSiklusFilter))
            ->whereHas('madrasah', function (Builder $q) use ($jenjangFilter, $kotaFilter, $madrasahIdsStatus, $search) {
                $q->when($jenjangFilter, fn ($qq) => $qq->where('jenjang_madrasah', $jenjangFilter))
                    ->when($kotaFilter, fn ($qq) => $qq->where('kota', $kotaFilter))
                    ->when($madrasahIdsStatus !== null, fn ($qq) => $qq->whereIn('id', $madrasahIdsStatus))
                    ->when($search, function ($qq) use ($search) {
                        $qq->where(function ($sub) use ($search) {
                            $sub->where('nama_madrasah', 'like', "%{$search}%")
                                ->orWhere('npsn', 'like', "%{$search}%");
                        });
                    });
            })
            ->orderBy('status')
            ->get();

        $daftarSiklus = $this->lengkapiInfoPendukung($daftarSiklus, $periode);

        $opsiFilter = [
            'jenjang' => Madrasah::whereNotNull('jenjang_madrasah')->distinct()->orderBy('jenjang_madrasah')->pluck('jenjang_madrasah'),
            'status_madrasah' => Madrasah::whereNotNull('status_madrasah')->distinct()->orderBy('status_madrasah')->pluck('status_madrasah'),
            'kota' => Madrasah::whereNotNull('kota')->distinct()->orderBy('kota')->pluck('kota'),
        ];

        $daftarPeriode = PrestasiSiklus::select('periode')->distinct()->orderByDesc('periode')->pluck('periode');

        $breadcrumb = breadcrumb(['Manajemen Status Siklus']);

        return view('siklus.index', compact(
            'daftarSiklus',
            'opsiFilter',
            'daftarPeriode',
            'periode',
            'jenjangFilter',
            'statusMadrasahFilter',
            'kotaFilter',
            'statusSiklusFilter',
            'search',
            'breadcrumb'
        ) + ['statusInfo' => self::STATUS_INFO]);
    }

    /*
    |--------------------------------------------------------------------------
    | LENGKAPI SETIAP BARIS SIKLUS DENGAN INFO PENDUKUNG (read-only) --
    | asesor yang ditugaskan & progres penilaian -- supaya admin punya
    | konteks cukup SEBELUM memutuskan override status secara manual.
    | Semua query di sini per-batch (bukan di dalam loop) untuk menghindari
    | N+1.
    |--------------------------------------------------------------------------
    */
    private function lengkapiInfoPendukung(Collection $daftarSiklus, int $periode): Collection
    {
        $madrasahIds = $daftarSiklus->pluck('madrasah_id');

        $assignments = AssignAsesor::with('asesor:id,nama')
            ->where('periode', $periode)
            ->whereIn('madrasah_id', $madrasahIds)
            ->get()
            ->keyBy('madrasah_id');

        $totalPrestasi = PrestasiSiswa::whereIn('madrasah_id', $madrasahIds)
            ->where('periode', $periode)
            ->selectRaw('madrasah_id, COUNT(*) as total')
            ->groupBy('madrasah_id')
            ->pluck('total', 'madrasah_id');

        $sudahDinilai = PrestasiSiswa::whereIn('madrasah_id', $madrasahIds)
            ->where('periode', $periode)
            ->whereHas('penilaianPrestasi')
            ->selectRaw('madrasah_id, COUNT(*) as total')
            ->groupBy('madrasah_id')
            ->pluck('total', 'madrasah_id');

        return $daftarSiklus->map(function (PrestasiSiklus $siklus) use ($assignments, $totalPrestasi, $sudahDinilai) {
            $assignment = $assignments->get($siklus->madrasah_id);
            $total = (int) ($totalPrestasi[$siklus->madrasah_id] ?? 0);
            $dinilai = (int) ($sudahDinilai[$siklus->madrasah_id] ?? 0);

            $siklus->info_asesor = $assignment?->asesor?->nama;
            $siklus->info_assignment_status = $assignment?->status;
            $siklus->info_total_prestasi = $total;
            $siklus->info_sudah_dinilai = $dinilai;
            $siklus->info_progress = $total > 0 ? round($dinilai / $total * 100) : 0;

            return $siklus;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | OVERRIDE STATUS SECARA MANUAL
    |--------------------------------------------------------------------------
    | PENTING: ini HANYA mengubah kolom status (+ timestamp terkait) pada
    | tabel prestasi_siklus itu sendiri. TIDAK ada sinkronisasi otomatis ke
    | assign_asesors.status maupun penilaian_prestasis.status -- itu
    | sengaja, supaya perubahan di sini tidak diam-diam mengubah tabel lain
    | tanpa admin sadar. Peringatan ini juga ditampilkan tebal di halaman.
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Request $request, PrestasiSiklus $prestasi_siklus)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                PrestasiSiklus::OPEN,
                PrestasiSiklus::SUBMITTED,
                PrestasiSiklus::ASSESSMENT,
                PrestasiSiklus::FINISHED,
            ])],
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        $statusLama = $prestasi_siklus->status;
        $statusBaru = $validated['status'];

        if ($statusLama === $statusBaru) {
            return back()->with('error', 'Status yang dipilih sama dengan status saat ini.');
        }

        // Timestamp penanda transisi (kolom di tabel prestasi_siklus sendiri,
        // BUKAN tabel lain) ikut diperbarui supaya histori transisi tetap
        // masuk akal walau statusnya di-override manual.
        $kolomTimestamp = match ($statusBaru) {
            PrestasiSiklus::SUBMITTED  => 'submitted_at',
            PrestasiSiklus::ASSESSMENT => 'assessment_started_at',
            PrestasiSiklus::FINISHED   => 'finished_at',
            default                    => null,
        };

        $dataUpdate = ['status' => $statusBaru];

        if ($kolomTimestamp) {
            $dataUpdate[$kolomTimestamp] = now();
        }

        $prestasi_siklus->update($dataUpdate);

        ActivityLogger::log(
            event: 'override_status_siklus',
            description: "Admin mengubah status siklus dari {$statusLama} menjadi {$statusBaru} secara manual (override)",
            subject: $prestasi_siklus,
            properties: [
                'madrasah_id' => $prestasi_siklus->madrasah_id,
                'periode'     => $prestasi_siklus->periode,
                'status_lama' => $statusLama,
                'status_baru' => $statusBaru,
                'alasan'      => $validated['alasan'],
                'diubah_oleh' => auth()->id(),
            ]
        );

        return back()->with(
            'success',
            'Status siklus berhasil diubah ke "' . self::STATUS_INFO[$statusBaru]['label'] . '". '
            . 'Ingat: status assignment asesor & penilaian TIDAK ikut berubah otomatis -- cek dan sesuaikan manual jika perlu.'
        );
    }
}