<?php

namespace App\Exports\Sheets;

use App\Models\AssignAsesor;
use App\Models\Madrasah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RingkasanPenilaianSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    /**
     * Nama asesor per madrasah_id untuk periode ini. Diisi di collection()
     * lalu dipakai di map() -- lihat catatan di collection() kenapa ini
     * TIDAK diambil lewat relasi Madrasah::assignAsesor().
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
        return 'Ringkasan';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Madrasah',
            'Jenjang',
            'Kota',
            'Asesor',
            'Total Prestasi',
            'Diakui',
            'Tidak Diakui',
            'Sudah Dinilai',
            'Belum Dinilai',
            'Total Nilai Akhir',
            'Rata-rata Nilai',
        ];
    }

    public function collection()
    {
        $madrasahs = Madrasah::query()
            ->where('jenjang_madrasah', $this->jenjang)
            ->when($this->madrasahId, fn ($q) => $q->where('id', $this->madrasahId))
            // "Terdaftar" = madrasah punya user aktif.
            ->whereHas('users', fn ($q) => $q->where('is_active', true))
            ->with([
                'prestasis' => fn ($q) => $q->where('periode', $this->periode)
                    ->with('penilaianPrestasi'),
            ])
            ->get();

        /*
        |----------------------------------------------------------------
        | PENTING: relasi Madrasah::assignAsesor() di model itu hasOne
        | yang SUDAH DIKUNCI ke PeriodeAktif::aktif() (periode aktif
        | SEKARANG), bukan relasi umum. Kalau $this->periode BUKAN
        | periode aktif saat ini (misal export data tahun lalu), pakai
        | relasi itu bakal selalu kosong. Jadi query langsung ke model
        | AssignAsesor, difilter periode yang benar-benar diminta.
        |----------------------------------------------------------------
        */
        $this->asesorPerMadrasah = AssignAsesor::where('periode', $this->periode)
            ->whereIn('madrasah_id', $madrasahs->pluck('id'))
            ->with('asesor:id,nama')
            ->get()
            ->keyBy('madrasah_id');

        // Urut natural (MAN 1, MAN 2 ... MAN 10) BUKAN alfabetis biasa --
        // alfabetis biasa akan naruh "MAN 10" sebelum "MAN 2".
        return $madrasahs
            ->sort(fn ($a, $b) => strnatcmp($a->nama_madrasah, $b->nama_madrasah))
            ->values();
    }

    public function map($madrasah): array
    {
        static $no = 0;
        $no++;

        $prestasis = $madrasah->prestasis;

        $totalPrestasi = $prestasis->count();
        $diakui = $prestasis->where('diakui', true)->count();
        $tidakDiakui = $prestasis->where('diakui', false)->count();

        $sudahDinilai = $prestasis->filter(fn ($p) => $p->penilaianPrestasi !== null)->count();
        $belumDinilai = $totalPrestasi - $sudahDinilai;

        /*
        |----------------------------------------------------------------
        | Total & rata-rata SENGAJA cuma dihitung dari prestasi yang
        | DIAKUI dan sudah punya nilai_akhir. Prestasi yang tidak diakui
        | tetap ikut di kolom "Total Prestasi" & "Tidak Diakui" di atas
        | (transparan, kelihatan ada berapa yang ditolak), tapi tidak
        | menyumbang ke nilai -- sesuai keputusan diskusi.
        |----------------------------------------------------------------
        */
        $nilaiDiakui = $prestasis
            ->where('diakui', true)
            ->map(fn ($p) => optional($p->penilaianPrestasi)->nilai_akhir)
            ->filter(fn ($nilai) => $nilai !== null);

        $totalNilaiAkhir = round((float) $nilaiDiakui->sum(), 2);
        $rataRata = $nilaiDiakui->count() > 0
            ? round($totalNilaiAkhir / $nilaiDiakui->count(), 2)
            : 0;

        $asesor = optional($this->asesorPerMadrasah->get($madrasah->id))->asesor;

        return [
            $no,
            $madrasah->nama_madrasah,
            $madrasah->jenjang_madrasah,
            $madrasah->kota,
            $asesor->nama ?? '-',
            $totalPrestasi,
            $diakui,
            $tidakDiakui,
            $sudahDinilai,
            $belumDinilai,
            $totalNilaiAkhir,
            $rataRata,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F2F2F2');

        return [];
    }
}