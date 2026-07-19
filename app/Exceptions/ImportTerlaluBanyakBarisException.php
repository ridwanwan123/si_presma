<?php

namespace App\Exceptions;

use Exception;

/*
|--------------------------------------------------------------------------
| DILEMPAR OLEH PrestasiSiswaImport::collection()
|--------------------------------------------------------------------------
| Begitu jumlah baris yang SUDAH terbaca melewati batas maksimal, exception
| ini dilempar untuk menghentikan proses baca file SAAT ITU JUGA -- bukan
| menunggu seluruh file (bisa puluhan ribu baris) selesai dibaca dulu baru
| ketahuan kelebihan di akhir.
|
| Karena Excel::import() dipanggil secara SINKRON (bukan queued), exception
| ini otomatis merambat naik dan langsung menghentikan pembacaan tanpa
| perlu penanganan khusus di Laravel Excel -- cukup ditangkap di
| PrestasiImportService::validateFile().
|--------------------------------------------------------------------------
*/
class ImportTerlaluBanyakBarisException extends Exception
{
    public function __construct(
        public readonly int $batasMaksimal
    ) {
        parent::__construct("Import dihentikan lebih awal: melebihi batas maksimal {$batasMaksimal} baris.");
    }
}