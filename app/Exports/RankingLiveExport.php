<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RankingLiveExport implements WithMultipleSheets
{
    public function __construct(
        private array $hasil,
        private int $periode,
        private ?string $jenjangFilter
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | SATU FILE, BEBERAPA SHEET -- karena juara sekarang ditentukan per
    | Bidang, tiap bidang layak punya sheet sendiri (bukan digabung jadi
    | satu tabel besar yang membingungkan siapa juara bidang apa).
    |--------------------------------------------------------------------------
    */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->hasil['per_bidang'] as $bidang => $papan) {
            $sheets[] = new RankingLiveSheetExport($papan, $bidang, false);
        }

        $sheets[] = new RankingLiveSheetExport($this->hasil['total'], 'Total Keseluruhan', true);

        return $sheets;
    }
}