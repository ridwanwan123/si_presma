<?php

namespace App\Exports\Sheets;

use App\Models\AssignAsesor;
use App\Models\PrestasiSiswa;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetailPenilaianSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    /**
     * Fallback nama asesor per madrasah_id untuk periode ini (dipakai kalau
     * prestasinya belum pernah dinilai sama sekali). Diisi di collection().
     */
    private $asesorPerMadrasah;

    public function __construct(
        private int $periode,
        private string $jenjang,
        private ?int $madrasahId
    ) {
    }

    public function title(): string
    {
        return 'Detail Per Prestasi';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Madrasah',
            'Bidang',
            'Nama Kegiatan',
            'Tingkat',
            'Kategori',
            'Juara',
            'Penyelenggara',
            'Kategori Penyelenggara',
            'Waktu Kegiatan',
            'Metode',
            'Skor Juknis',
            'Diakui',
            'Persentase',
            'Nilai Akhir',
            'Catatan Asesor',
            'Nama Asesor',
            'Status Penilaian',
        ];
    }

    public function collection()
    {
        $prestasis = PrestasiSiswa::query()
            ->where('periode', $this->periode)
            ->whereHas('madrasah', function ($q) {
                $q->where('jenjang_madrasah', $this->jenjang)
                    // "Terdaftar" = madrasah punya user aktif.
                    ->whereHas('users', fn ($qq) => $qq->where('is_active', true))
                    ->when($this->madrasahId, fn ($qq) => $qq->where('id', $this->madrasahId));
            })
            ->with([
                'madrasah:id,nama_madrasah',
                'penilaianPrestasi.assignAsesor.asesor:id,nama',
            ])
            ->get();

        /*
        |----------------------------------------------------------------
        | Fallback nama asesor (kalau prestasi belum pernah dinilai) TIDAK
        | diambil lewat relasi Madrasah::assignAsesor() -- relasi itu
        | hasOne yang DIKUNCI ke PeriodeAktif::aktif(), jadi salah kalau
        | $this->periode bukan periode aktif saat ini. Query langsung ke
        | AssignAsesor dengan periode yang benar.
        |----------------------------------------------------------------
        */
        $this->asesorPerMadrasah = AssignAsesor::where('periode', $this->periode)
            ->whereIn('madrasah_id', $prestasis->pluck('madrasah_id')->unique())
            ->with('asesor:id,nama')
            ->get()
            ->keyBy('madrasah_id');

        // Urut nama madrasah (natural sort, MAN 1..MAN 10) sesuai keputusan
        // diskusi -- jenjang sudah difilter duluan jadi urutan tambahan per
        // bidang di dalamnya cukup buat rapi bacanya.
        return $prestasis
            ->sort(function ($a, $b) {
                $cmp = strnatcmp($a->madrasah->nama_madrasah, $b->madrasah->nama_madrasah);

                return $cmp !== 0 ? $cmp : strcmp((string) $a->bidang_prestasi, (string) $b->bidang_prestasi);
            })
            ->values();
    }

    public function map($prestasi): array
    {
        static $no = 0;
        $no++;

        $penilaian = $prestasi->penilaianPrestasi;

        // Prioritas nama asesor: dari record penilaian (paling akurat, siapa
        // yang benar-benar menilai). Kalau belum dinilai, fallback ke
        // assignment madrasah untuk periode ini.
        $namaAsesor = optional(optional($penilaian)->assignAsesor)->asesor->nama
            ?? optional($this->asesorPerMadrasah->get($prestasi->madrasah_id))->asesor->nama
            ?? '-';

        $statusPenilaian = match (true) {
            $penilaian === null => 'Belum Dinilai',
            $penilaian->status === 'draft' => 'Draft',
            $penilaian->status === 'completed' => 'Selesai',
            default => (string) $penilaian->status,
        };

        return [
            $no,
            $prestasi->madrasah->nama_madrasah,
            $prestasi->bidang_prestasi,
            $prestasi->nama_kegiatan,
            $prestasi->tingkat,
            $prestasi->kategori_kegiatan,
            $prestasi->juara,
            $prestasi->lembaga_penyelenggara,
            $prestasi->kategori_penyelenggara,
            $prestasi->waktu_kegiatan ? Carbon::parse($prestasi->waktu_kegiatan)->format('d-m-Y') : '-',
            $prestasi->metode_pelaksanaan,
            (float) $prestasi->skor,
            $prestasi->diakui ? 'Diakui' : 'Tidak Diakui',
            $penilaian->persentase ?? null,
            $penilaian->nilai_akhir ?? null,
            $penilaian->catatan ?? null,
            $namaAsesor,
            $statusPenilaian,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:R1')->getFont()->setBold(true);
        $sheet->getStyle('A1:R1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F2F2F2');

        return [];
    }
}