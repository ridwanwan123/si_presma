<?php

namespace App\Exports;

use App\Exports\Sheets\DetailPenilaianSheet;
use App\Exports\Sheets\RingkasanPenilaianSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/*
|--------------------------------------------------------------------------
| LAPORAN HASIL PENILAIAN ASESOR
|--------------------------------------------------------------------------
| Sheet 1 (Ringkasan)     : satu baris per madrasah, total & rata-rata.
| Sheet 2 (Detail)        : satu baris per prestasi, breakdown lengkap
|                           termasuk persentase/nilai akhir/catatan asesor.
|
| Beda dengan PrestasiMadrasahExport (data mentah 1 madrasah, format
| resmi buat dicetak) -- export ini rekap LINTAS madrasah untuk
| superadmin, jadi sengaja flat table biar gampang difilter/pivot lagi
| di Excel, bukan dokumen resmi siap cetak.
|--------------------------------------------------------------------------
*/
class LaporanPenilaianExport implements WithMultipleSheets
{
    public function __construct(
        private int $periode,
        private string $jenjang,
        private ?int $madrasahId = null
    ) {
    }

    public function sheets(): array
    {
        return [
            new RingkasanPenilaianSheet($this->periode, $this->jenjang, $this->madrasahId),
            new DetailPenilaianSheet($this->periode, $this->jenjang, $this->madrasahId),
        ];
    }
}