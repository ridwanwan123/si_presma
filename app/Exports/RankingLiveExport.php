<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RankingLiveExport implements WithMultipleSheets
{
    // Urutan sheet berdasarkan jenjang pendidikan (bukan alfabetis) --
    // sama seperti urutan tab di halaman web. Jenjang di luar daftar ini
    // tetap ikut diekspor, cuma ditaruh paling akhir.
    private const URUTAN_JENJANG = ['RA', 'MI', 'MTs', 'MA'];

    public function __construct(
        private array $hasil,
        private int $periode
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | SATU FILE, BEBERAPA SHEET -- disusun PER JENJANG lalu PER BIDANG
    | (mengikuti tab di halaman web), karena juara ditentukan per jenjang x
    | per bidang -- bukan digabung. Tiap jenjang juga dapat satu sheet
    | "Total <jenjang>" sebagai referensi (bukan penentu juara).
    |--------------------------------------------------------------------------
    */
    public function sheets(): array
    {
        $sheets = [];

        $perJenjang = collect($this->hasil['per_jenjang'] ?? [])
            ->sortBy(function ($dataJenjang, $jenjang) {
                $index = array_search($jenjang, self::URUTAN_JENJANG);
                return $index === false ? 999 : $index;
            });

        foreach ($perJenjang as $jenjang => $dataJenjang) {
            foreach ($dataJenjang['per_bidang'] as $bidang => $papan) {
                $sheets[] = new RankingLiveSheetExport(
                    $papan,
                    $jenjang . ' - ' . $bidang,
                    false
                );
            }

            $sheets[] = new RankingLiveSheetExport(
                $dataJenjang['total'],
                'Total ' . $jenjang,
                true
            );
        }

        return $sheets;
    }
}